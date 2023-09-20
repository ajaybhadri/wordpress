/* Put this code inside functions.php to create a shortcode for custom menu .Use shortcode where you want to display menu.Add class is_mega_menu to menu item if you have a category in menu.It will read the name of the title of menu and display subcategories from order A to Z if it has sub- categories */
/* Bootstrap version used : 4 */

function categories_menu_mobile(){
  $primaryNav = wp_get_nav_menu_items('Current Menu'); 
  // Filter out only the parent menu items
  $parentMenuItems = array_filter($primaryNav, function ($item) {
    return $item->menu_item_parent == 0;
  });

  echo '<nav id="" class="navbar navbar-expand-lg navbar-light bg-light"><button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
  <span class="navbar-toggler-icon"></span>
</button><div class="collapse navbar-collapse" id="navbarSupportedContent"><ul class="navbar-nav mr-auto">';
  foreach($parentMenuItems as $key=>$value){
    $classes = $value->classes;
    
    if(is_array($value->classes) && in_array("is_mega_menu",$value->classes)){
        $is_mega_menu = true;
        $pterm = get_term_by( 'name', $value->title, 'product_cat' );
        if($pterm){
          $parent_termID = $pterm->term_id;
        }
        $terms = get_terms( array(
              'taxonomy'   => 'product_cat',
              'parent'     => $parent_termID ,
              'hide_empty' => false,
              'orderby' => 'name',
              'order' => 'ASC'
        ) ); 
        $startLetter = ''; $count=0;
        if ( !empty($terms)) {
          //mega menu content
          foreach( range( 'A', 'Z' ) as $letter ) {
              $mega_menu_html .='<a class="nav-link disabled" href="#">'.$letter.'</a>';
              foreach($terms as $term){ 
              $title = $term->name;
              $initial = strtoupper( substr( $title, 0, 1 ) );
              if( $initial == $letter ){
                $mega_menu_html .= '<a class="dropdown-item" href="'.get_term_link( $term->slug, 'product_cat' ).'"><span>'.$term->name.'</span></a>';
              }
              }
              $mega_menu_html .= '<div class="dropdown-divider"></div>';
          }
          //mega emnu content end
        }
    }else{
      $is_mega_menu = false;
      $mega_menu_html = false;
    }
    if(has_sub_menu('primary',$value->ID) || $mega_menu_html){
           echo "<li class='nav-item dropdown'>";
           echo '<a class="nav-link dropdown-toggle" href="'.$value->url.'" id="navbarDropdown'.$value->ID.'" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
           '.$value->title.'
         </a>
         <div class="dropdown-menu" aria-labelledby="navbarDropdown'.$value->ID.'">';
          echo has_sub_menu_html('primary',$value->ID);
          echo $mega_menu_html;
           echo '</div>';
           echo "</li>";
    }else{
           echo "<li class='nav-item'><a href='".$value->title."' class='nav-link'>".$value->title."</a></li>"; 
    }
  
  }
  echo "</ul></div></nav>";
}

add_shortcode( 'categories_menu_mobile', 'categories_menu_mobile' );



function has_sub_menu($menu_location,$id)
{
	//Get proper menu
	$menuLocations = get_nav_menu_locations();
	$menuID = $menuLocations[$menu_location];
	$menu_items = wp_get_nav_menu_items($menuID);

	//Go through and see if this is a parent
	foreach ($menu_items as $menu_item) {
		if ((int)$menu_item->menu_item_parent === $id) {
			return true;
		}
	}
	return false;
}


function has_sub_menu_html($menu_location,$id)
{
	//Get proper menu
	$menuLocations = get_nav_menu_locations();
	$menuID = $menuLocations[$menu_location];
	$menu_items = wp_get_nav_menu_items($menuID);
  $html = '';
	//Go through and see if this is a parent
	foreach ($menu_items as $menu_item) {
		if ((int)$menu_item->menu_item_parent === $id) {
			
			$html .= '<a class="dropdown-item" href="'.$menu_item->url.'">'.$menu_item->title.'</a>';
		}
	}
	return $html;
}
