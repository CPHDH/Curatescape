<?php
/**
 * Simple Pages
 *
 * @copyright Copyright 2008-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Get the public navigation links for children pages of a parent page.
 *
 * @uses public_url()
 * @param integer|null The id of the parent page.  If null, it uses the current simple page
 * @param string The method by which you sort pages. Options are 'order' (default) and 'alpha'.
 * @param boolean Whether to return only published pages.
 * @param array Array with keys of parent_id and values = array(SimplePages)
 *              that have that parent_id. Used internally.
 * @return array The navigation links.
 */
function simple_pages_get_links_for_children_pages($parentId = null, $sort = 'order', $requiresIsPublished = false,
                                                   $pages = null)
{
    if ($parentId === null) {
        $parentPage = get_current_record('simple_pages_page', false);
        if ($parentPage) {
            $parentId = $parentPage->id;
        } else {
            $parentId = 0;
        }
    }

    if (! isset($pages)) {
        $findBy = array('sort' => $sort);
        if ($requiresIsPublished) {
            $findBy['is_published'] = $requiresIsPublished ? 1 : 0;
        }
        $pages = get_db()->getTable('SimplePagesPage')->findBy($findBy);

        $parentIdToPages = array();
        foreach ($pages as $page) {
            if (! isset($parentIdToPages[$page->parent_id])) {
                $parentIdToPages[$page->parent_id] = array();
            }
            $parentIdToPages[$page->parent_id][] = $page;
        }
        $pages = $parentIdToPages;
    }

    $navLinks = array();

    if (isset($pages[$parentId])) {
        foreach ($pages[$parentId] as $page) {
            $uri = public_url($page->slug);
            $subNavLinks = simple_pages_get_links_for_children_pages(
                $page->id, $sort, $requiresIsPublished, $pages
            );

            if (count($subNavLinks) > 0) {
                $navLinks[] = array(
                    'label' => $page->title,
                    'uri' => $uri,
                    'pages' => $subNavLinks
                );
            } else {
                $navLinks[] = array(
                    'label' => $page->title,
                    'uri' => $uri,
                );
            }
        }
    }

    return $navLinks;
}

/**
* Returns a nested unordered list of SimplePage links
*
* @uses simple_pages_get_links_for_children_pages()
* @uses nav()
* @param integer|null The id of the parent page.  If null, it uses the current simple page
* @param string The method by which you sort pages. Options are 'order' (default) and 'alpha'.
* @param boolean Whether to return only published pages.
* @return string
*/
function simple_pages_navigation($parentId = 0, $sort = 'order', $requiresIsPublished = true)
{
    $html = '';
    $childPageLinks = simple_pages_get_links_for_children_pages($parentId, $sort, $requiresIsPublished);
    if ($childPageLinks) {
        $html .= '<div class="simple-pages-navigation">' . "\n";
        $html .= nav($childPageLinks);
        $html .= '</div>' . "\n";
    }
    return $html;
}

/**
 * Returns a breadcrumb for a given page.
 *
 * @uses public_url(), html_escape()
 * @param integer|null The id of the page.  If null, it uses the current simple page.
 * @param string $separator The string used to separate each section of the breadcrumb.
 * @param boolean $includePage Whether to include the title of the current page.
 */
function simple_pages_display_breadcrumbs($pageId = null, $seperator=' > ', $includePage=true)
{
    $html = '';

    if ($pageId === null) {
        $page = get_current_record('simple_pages_page', false);
    } else {
        $page = get_db()->getTable('SimplePagesPage')->find($pageId);
    }

    if ($page) {
        $ancestorPages = get_db()->getTable('SimplePagesPage')->findAncestorPages($page->id);
        $bPages = array_merge(array($page), $ancestorPages);

        // make sure all of the ancestors and the current page are published
        foreach($bPages as $bPage) {
            if (!$bPage->is_published) {
                $html = '';
                return $html;
            }
        }

        // find the page links
        $pageLinks = array();
        foreach($bPages as $bPage) {
            if ($bPage->id == $page->id) {
                if ($includePage) {
                    $pageLinks[] = html_escape($bPage->title);
                }
            } else {
                $pageLinks[] = '<a href="' . public_url($bPage->slug) .  '">' . html_escape($bPage->title) . '</a>';
            }
        }
        $pageLinks[] = '<a href="'. public_url('') . '">' . __('Home') . '</a>';
        
        $seperator = '<span class="separator" aria-hidden="true">' . html_escape($seperator) . '</span>';

        // create the bread crumb
        $html .= implode($seperator, array_reverse($pageLinks));
    }
    return $html;
}

function simple_pages_display_hierarchy($parentPageId = 0, $partialFilePath = 'index/browse-hierarchy-page.php')
{
    $html = '';
    $childrenPages = get_db()->getTable('SimplePagesPage')->findChildrenPages($parentPageId);
    if (count($childrenPages)) {        
        $html .= '<ul>';
        foreach($childrenPages as $childPage) {
            $html .= '<li>';
            $html .= get_view()->partial($partialFilePath, array('simple_pages_page' => $childPage));
            $html .= simple_pages_display_hierarchy($childPage->id, $partialFilePath);
            $html .= '</li>';
        }
        $html .= '</ul>';
    }
    return $html;
}

/**
 * Returns the earliest ancestor page for a given page.
 *
 * @param integer|null The id of the page. If null, it uses the current simple page.
 * @return SimplePagesPage|null
 */
function simple_pages_earliest_ancestor_page($pageId)
{
    if ($pageId === null) {
        $page = get_current_record('simple_pages_page');
    } else {
        $page = get_db()->getTable('SimplePagesPage')->find($pageId);
    }

    $pageAncestors = get_db()->getTable('SimplePagesPage')->findAncestorPages($page->id);
    return end($pageAncestors);
}

function simple_pages_get_parent_options($page)
{
    $valuePairs = array('0' => __('Main Page (No Parent)'));
    $potentialParentPages = get_db()->getTable('SimplePagesPage')->findPotentialParentPages($page->id);
    foreach($potentialParentPages as $potentialParentPage) {
        if (trim($potentialParentPage->title) != '') {
            $valuePairs[$potentialParentPage->id] = $potentialParentPage->title;
        }
    }
    return $valuePairs;
}
