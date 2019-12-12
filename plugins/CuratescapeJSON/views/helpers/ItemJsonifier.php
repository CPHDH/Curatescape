<?php

class CuratescapeJSON_View_Helper_ItemJsonifier extends Zend_View_Helper_Abstract
{
	public function __construct()
	{
		// Determine if the item type schemas have custom elements
		$this->hasStory = element_exists('Item Type Metadata','Story');
		$this->hasSubtitle = element_exists('Item Type Metadata','Subtitle');
		$this->hasSponsor = element_exists('Item Type Metadata','Sponsor');
		$this->hasAccessInfo = element_exists('Item Type Metadata','Access Information');
		$this->hasStreetAddress = element_exists('Item Type Metadata','Street Address');
		$this->hasVisibility = element_exists('Item Type Metadata','Access status');
		$this->hasLede = element_exists('Item Type Metadata','Lede');
		$this->hasWebsite = element_exists('Item Type Metadata','Official Website');
		// $this->hasFactoid = element_exists('Item Type Metadata','Factoid');
		$this->hasRelatedResources = element_exists('Item Type Metadata','Related Resources');		
		// $this->storage = Zend_Registry::get('storage');
		}

	private static function getDublinText( $element, $formatted = false )
	{
		$raw = metadata( 'item', array( 'Dublin Core', $element ) );
		if( ! $formatted )
			$raw = strip_formatting( $raw );

		return html_entity_decode( $raw );
	}

	private static function getItemTypeText( $element, $formatted = false )
	{
		$raw = metadata( 'item', array( 'Item Type Metadata', $element ) );
		if( ! $formatted )
			$raw = strip_formatting( $raw );

		return html_entity_decode( $raw );
	}
	
	private static function getContributor($item)
	{
        $contribItem = get_db()->getTable('ContributionContributedItem')->findByItem($item);
        $name=array();
        if($contribItem->anonymous) {
            $name[] = "Anonymous";
        } else if($contribItem->Contributor->name){
            $name[] = $contribItem->Contributor->name;
        }
        
        return $name;
		
	}
	
	public function itemJsonifier( $item, $isExtended = false )
	{
		// Skip items that don't have a location
		if($location = get_db()->getTable( 'Location' )->findLocationByItem( $item, true )){
			
			/* Core metadata */
			
			$titles = metadata( 'item', array( 'Dublin Core', 'Title' ), array( 'all' => true ) );
	
			$itemMetadata = array(
				'id'          => $item->id,
				'featured'    => $item->featured,
				'modified'	  => $item->modified,
				'latitude'    => $location[ 'latitude' ],
				'longitude'   => $location[ 'longitude' ],
				'title'       => $titles[0] ? trim(html_entity_decode( strip_formatting( $titles[0] ) )) : 'Untitled',
			);
				
			if( $this->hasStreetAddress){
				$itemMetadata['address']=self::getItemTypeText('Street Address',true);
			}

			if( $this->hasVisibility){
				$itemMetadata['visibility']=self::getItemTypeText('Access status');
			}			
	
			if(metadata($item, 'has thumbnail')){
				$itemMetadata[ 'thumbnail' ] = (preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', item_image('square_thumbnail'), $result)) ? array_pop($result) : null;
			}
	
			/* Extended Metadata */
	
			if($isExtended){
	
				$authors = metadata( 'item', array( 'Dublin Core', 'Creator' ),array( 'all' => true ) );
				$authorsStripped = array();
				foreach( $authors as $auth )
				{
					$authorsStripped[] = html_entity_decode( strip_formatting( $auth ) );
				}
				
				$itemMetadata['creator']=$authorsStripped;

				if(!$itemMetadata['creator'] && plugin_is_active('Contribution') && plugin_is_active('GuestUser'))
				{
					$itemMetadata['creator']=self::getContributor($item);
				}	
	
				if( $this->hasStory)
				{
					$itemMetadata['description']=self::getItemTypeText('Story',true);
				}
	
				if(!$itemMetadata['description'])
				{
					$itemMetadata['description']=self::getDublinText( 'Description', true );
				}
	
				if( $this->hasSponsor )
				{
					$itemMetadata['sponsor']=self::getItemTypeText('Sponsor');
				}
	
				if( $this->hasSubtitle )
				{
					$itemMetadata['subtitle'] = self::getItemTypeText('Subtitle');
				}
	
				if( !$itemMetadata['subtitle'] && count( $titles ) > 1 )
				{
					$itemMetadata['subtitle'] = html_entity_decode( strip_formatting( $titles[1] ) );
				}
	
				if( $this->hasAccessInfo )
				{
					$itemMetadata[ 'accessinfo' ] = self::getItemTypeText('Access Information');
				}
				
				if( $this->hasLede )
				{
					$itemMetadata['lede']=self::getItemTypeText('Lede');
				}				
				
				if( $this->hasWebsite )
				{
					$itemMetadata[ 'website' ] = self::getItemTypeText('Official Website',true);
				}

				if( $this->hasRelatedResources )
				{
					$resources = array();
					$arr = metadata( 'item', array( 'Item Type Metadata', 'Related Resources' ), array( 'all' => true ) );
					foreach($arr as $resource){
						$resources[]= trim(html_entity_decode($resource));
					}
					
					$itemMetadata[ 'related_resources' ]=$resources;
				}
				
	
				// Add files
				$files = array();
				foreach( $item->Files as $file )
				{
					
					$mimetype = metadata( $file, 'MIME Type' );
	
					$filedata = array(
						'id'        => $file->id,
						'mime-type' => $mimetype,
						);
	
					$title = metadata( $file, array( 'Dublin Core', 'Title' ) );
					$filedata[ 'title' ] = $title ? strip_formatting( $title ) : 'Untitled';
	
	
					if( $file->hasThumbnail() )
					{
						$filedata[ 'thumbnail' ] = $file->getWebPath( 'square_thumbnail' );
					}
	
					if( strpos( $mimetype, 'image/' ) === 0 )
					{
						$path = $file->getWebPath( 'fullsize' );
					}else{
						$path = $file->getWebPath( 'original' );
					}
	
					$caption = array();
					if( $description = metadata( $file, array( 'Dublin Core', 'Description' ) ) )
					{
						$caption[] = $description;
					}
	
					if( $source = metadata( $file, array( 'Dublin Core', 'Source' ) ))
					{
						$caption[] = $source;
					}
	
					if( $creator = metadata( $file, array( 'Dublin Core', 'Creator' ) ) )
					{
						$caption[] = $creator;
					}
	
					if( count( $caption ) )
					{
						$filedata[ 'description' ] = implode( " | ", $caption );
					}
	
					$files[ $path ] = $filedata;
	
				}
	
				if( count( $files ) > 0 )
				{
					$itemMetadata[ 'files' ] = $files;
				}
			}
	
			return $itemMetadata;
		}else{
			return false;
		}
	}
}