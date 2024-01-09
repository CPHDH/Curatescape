<nav id="admin-bar" aria-label="Admin Navigation">
<?php if ($user = current_user()) {
    $links = array(
        array(
            'label' => __('Account'),
            'uri' => admin_url('/users/edit/'.$user->id)
        ),
        array(
            'label' => __('Site Admin'),
            'uri' => admin_url('/')
        ),
        array(
            'label' => __('Log Out'),
            'uri' => url('/users/logout')
        )
    );
    $url=current_url();
    $r = preg_match_all("/.*?(\d+)$/", $url, $matches);
    if ($r==1) {
        $edit=array(
            'label'=>__('Edit Record'),
            'uri'=>admin_url($matches[0][0]),
            'class'=>'highlight'
        );
        array_unshift($links, $edit);
    }
    echo nav($links, 'public_navigation_admin_bar');
}
?>
</nav>
