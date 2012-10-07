<?php
/****************************************************************************GENERAL LIBRARY****************************************************************************/

// Enable excerption in page
add_post_type_support( 'page', 'excerpt' );

//Disable admin bar
show_admin_bar(false);

//Register sidebar
if ( function_exists('register_sidebar') ) register_sidebar();

//Register wordpress menu
if ( !function_exists( 'register_nav_menu' ) ) {
	register_nav_menu( 'main-menu', 'Main Menu' );
}

//Limit excerpt
function wrapExcerpt($str, $len) {
  //$chars = preg_split('/([\s\-_,:;?!\/\(\)\[\]{}<>\r\n"]|(?<!\d)\.(?!\d))/', $str, null, PREG_SPLIT_NO_EMPTY);
  $str = preg_replace("/<img[^>]+\>/i", "", $str);
  $chars = explode(" ", $str);
  
  $count = 0; $result = "";
  foreach($chars as $val) {
    $count = $count + strlen($val);
    $result = $result . $val . " ";
   if($count > $len) break;
  }
  $result = substr($result, 0 , strlen($result) - 1);
  
  return $result;
}

function getPostFromURL($url) { //Return $post->____. $url string must be: http://www.example.com/.../this-post/ in the same database ONLY
  global $wpdb;
  $url = trim($url);
  $url = substr($url, strrpos($url, '/', -2));
  $url = str_replace("/", "", $url);
  $postID = $wpdb->get_row("SELECT `ID` FROM $wpdb->posts WHERE `post_status` = 'publish' AND `post_name` = '" . $url ."'");
  $post = get_post($postID->ID);
  return $post;
}

function getPostIDfromURL($url) { //Return post-ID. $url string must be: http://www.example.com/.../this-post/ in the same database
  global $wpdb;
  $url = trim($url);
  $url = substr($url, strrpos($url, '/', -2));
  $url = str_replace("/", "", $url);
  $postID = $wpdb->get_row("SELECT `ID` FROM $wpdb->posts WHERE `post_status` = 'publish' AND `post_name` = '" . $url ."'");
  return $postID->ID;
}

function getSlug() {
  global $post;
  if (is_single() || is_page()) { return $post->post_name; }
  else { return "";}
}

function getParentSlug() {
  global $post;
  if($post->post_parent == 0) return '';
  $parent_post = get_post($post->post_parent);
  return $parent_post->post_name;
}

function getParentPage(){
  global $post;
  $selfSlug = explode(" ", get_field('header', $post->ID));
  return strtolower($selfSlug[0]);
}


if ( !function_exists( 'the_parent_categories' ) ) { //Return category_name::thumbnail::description::slug
  function the_parent_categories($lang) {
    if ( $lang == 'th' ) { $slug = 'th'; }
    else { $slug = 'en'; }
    $obj = get_category_by_slug( $slug ); 
    $childcategories = get_categories('parent=' . $obj->cat_ID . '&hide_empty=0');
    $result = array();
    foreach ($childcategories as $category) {
      $desc = preg_split("#\n\s*\n#Uis", $category->category_description);
      $combine_desc = $category->name . "::" . $desc[0] . "::" . $desc[1] . "::" .$category->slug;
      array_push($result, $combine_desc);
    }
    return $result;
  }
}
?>