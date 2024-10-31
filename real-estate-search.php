<?php
/*
Plugin Name: Real Estate Search
Plugin URI: http://www.altijdzon.nl/real-estate-plugin/
Description: Real Estate listings are easiest to browse/search with consecutive tag selections. Browse listings that meet multiple tag criteria. <a href="admin.php?page=real-estate-search/real-estate-search.php">Edit default listing types here</a>. This plugin complements real-estate plugin by same author. (Do you have problems with wrong count numbers? <a href="http://blog.andreineculau.com/2008/07/delete-wordpress-26-revisions/">read this</a> - requires mysql version 5.+
Version: 1.8.1
Author: dom rep
Author URI: http://www.altijdzon.nl/
*/

/*
    Real Estate - plugin adds a property image gallery and information to Wordpress blog posts
    Copyright (C) 2009 altijdzon.nl (email: dom.rep.3000_WITHOUT_THIS_ANTISPAM_@gmail.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

global $res_locations, $res_properties, $res_prices,$res_locationss, $res_propertiess, $res_pricess;
$res_locationss = get_option('res_locationss');
$res_propertiess = get_option('res_propertiess');
$res_pricess = get_option('res_pricess');

// create custom plugin settings menu
add_action('admin_menu', 'res_create_menu');

function res_create_menu() {

	//create new top-level menu
	add_menu_page('Real Estate Search Plugin Settings', 'Real Estate Search Settings', 'administrator', __FILE__, 'res_settings_page');

	//call register settings function
	add_action( 'admin_init', 'register_res_mysettings' );
}


function register_res_mysettings() {
	//register our settings
	register_setting( 'res-settings-group', 'res_locationss' );
	register_setting( 'res-settings-group', 'res_propertiess' );
	register_setting( 'res-settings-group', 'res_pricess' );
}

function res_settings_page() {
?>
<div class="wrap">
<h2>Real Estate Search Plugin</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'res-settings-group' ); ?>
    <table class="form-table">
         
        <tr valign="top">
        <th scope="row">INSTRUCTIONS</th>
        <td><p>Here you need to define which of all post tags belong to following 3 groups: locations, properties, prices. Tags specified below will appear in the tag browsing menu on listings pages.</p><p>To link to most important tags from the sidebar, I recommend that you define links (within appropriate link categories) that point to them.</p></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">LOCATIONS to browse (each in new line)</th>
        <td><textarea name="res_locationss" rows="10" cols="30" ><?php echo get_option('res_locationss'); ?></textarea></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">PROPERTIES to browse (each in new line)</th>
        <td><textarea name="res_propertiess" rows="10" cols="30" ><?php echo get_option('res_propertiess'); ?></textarea></td>
        </tr>

        <tr valign="top">
        <th scope="row">PRICES to browse (each in new line)</th>
        <td>
        <p>
        <b>!!! here keep dashes instead of spaces !!! (write tag slugs instead of names)</b>
		</p>
		<textarea name="res_pricess" rows="5" cols="30" ><?php echo get_option('res_pricess'); ?></textarea>
		</td>
        </tr>

    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php }

function res_get_tags($content = '') {

global $res_locations, $res_properties, $res_prices,$res_locationss, $res_propertiess, $res_pricess;

$res_locations = array_filter(array_unique(array_map('trim',explode('
',$res_locationss))));
$res_properties = array_filter(array_unique(array_map('trim',explode('
',$res_propertiess))));
$res_prices = array_filter(array_unique(array_map('trim',explode('
',$res_pricess))));

$tag_query = substr($_SERVER['REQUEST_URI'],strpos($_SERVER['REQUEST_URI'],get_option('tag_base'))+strlen(get_option('tag_base')));
$tag_query = substr($tag_query,1,strpos($tag_query,'/',2)-1);

if(stristr($tag_query,'+'))$maintag = substr($tag_query,0,strpos($tag_query,'+'));
else $maintag = $tag_query;

$results = mysql_query("SELECT wp_terms.name FROM wp_term_taxonomy, wp_terms WHERE wp_term_taxonomy.term_id = wp_terms.term_id AND wp_term_taxonomy.taxonomy = 'post_tag' and wp_terms.slug = '".addslashes($maintag)."' ") or die(mysql_error());
while ($row = mysql_fetch_assoc($results)){
	extract($row, EXTR_PREFIX_ALL, "p");
	$mainname = $p_name;
}


//$maintag = explode(',',$maintag);
$res_locations = array_map('res_strtolowerdash',$res_locations);
$res_properties = array_map('res_strtolowerdash',$res_properties);
uasort($res_locations, "length_sort");uasort($res_properties, "length_sort");
$in = '';
$tq = $tag_query;if(in_array($maintag,$res_properties))$in = ' in';
$tqpr = str_ireplace($res_prices,'',$tq);$tqpr = trim($tqpr,' +');
if(!$in){$tqp = str_ireplace($res_properties,'',$tqpr);$tqp = trim($tqp,' +');}
else {$tql = str_ireplace($res_locations,'',$tqpr);$tql = trim($tql,' +');}

$terms = "<p class=\"tagnav\">You are now in <b><a class=\"tagnav\" href=\"".get_option('siteurl')."/".get_option('tag_base')."/$maintag/\">".str_replace(array('M2',' Or ',' And '),array('m2',' or ',' and '),ucwords(selective_dash($mainname)))."</a></b>";
if($tq!=$maintag) $terms .= " &gt; <b><a class=\"tagnav\" href=\"".get_option('siteurl')."/".get_option('tag_base')."/$tq/\">".str_replace(array('M2',' Or ',' And '),array('m2',' or ',' and '),ucwords(str_ireplace(selective_dash($maintag),'',selective_dash($tq))))."</a></b></p>";
else $terms .= "</p>";

if(in_array($maintag,$res_locations)||in_array($maintag,$res_properties)){
	if(in_array($maintag,$res_locations)){
		$terms .='<p class="tagnav"><strong>Make your choice:</strong> ';
		$showthesea = @res_find_counts($res_properties,$maintag);
		$showthese = @$showthesea[0];
		$namethese = @$showthesea[1];
		foreach($showthese as $what => $count){
			$lrp = strtolower($what); $lrpn = strtolower($namethese[$what]); $terms .= "<a class=\"tagnav\" href=\"".get_option('siteurl')."/".get_option('tag_base')."/$tqp+{$lrp}/\">".(selective_dash($lrpn))."</a> ($count), ";
		}
		$terms .='</p>';
	}
	elseif(in_array($maintag,$res_properties)){
		$terms .='<p class="tagnav"><strong>Make your choice:</strong> ';
		$showthesea = @res_find_counts($res_locations,$maintag);
		$showthese = @$showthesea[0];
		$namethese = @$showthesea[1];
		foreach($showthese as $what => $count){
			$lrp = strtolower($what); $lrpn = strtolower($namethese[$what]); $terms .= "<a class=\"tagnav\" href=\"".get_option('siteurl')."/".get_option('tag_base')."/$tql+{$lrp}/\">".(selective_dash($lrpn))."</a> ($count), ";
		}
		$terms .='</p>';
	}

	$terms .='<p class="tagnav"><strong>Select price range (in USD):</strong> ';
	$showthesea = @res_find_counts($res_prices,$maintag);
	$showthese = @$showthesea[0];
	$namethese = @$showthesea[1];
	foreach($showthese as $what => $count){
		$lrp = strtolower($what); $lrpn = strtolower($namethese[$what]); $terms .= "<a class=\"tagnav\" href=\"".get_option('siteurl')."/".get_option('tag_base')."/$tqpr+{$lrp}/\">".$lrpn."</a>, ";
	}
	$terms .='</p>';
}else {

	$terms .='<p class="tagnav"><strong>Select price range (in USD):</strong> ';
	foreach($res_prices as $what){
		$lrp = strtolower($what); $terms .= "<a class=\"tagnav\" href=\"".get_option('siteurl')."/".get_option('tag_base')."/$tqpr+{$lrp}/\">".$lrp."</a>, ";
	}
	$terms .='</p>';

}

if (is_tag()) {
echo $terms;//.$content;
}
//else return $content;
add_filter('wp_title', 'res_tag_title');

}

function res_strtolowerdash($x){return strtolower(str_replace(' ','-',$x));}
//add_filter('the_content','res_get_tags');

function res_tag_title($orig,$full){return str_replace($orig.' | ',$full.' | ',$title);}

function res_find_counts($related,$current){
	global $wpdb, $res_locations, $res_properties, $res_prices;
	$terms = array();$term_names = array();$counts = array();
	$results = mysql_query("SELECT wp_terms.slug, wp_terms.name, wp_term_taxonomy.term_taxonomy_id FROM wp_term_taxonomy, wp_terms WHERE wp_term_taxonomy.term_id = wp_terms.term_id AND wp_term_taxonomy.taxonomy = 'post_tag' ") or die(mysql_error());
	while ($row = mysql_fetch_assoc($results)){
		extract($row, EXTR_PREFIX_ALL, "p");
		$terms[$p_slug] = $p_term_taxonomy_id;
		$term_names[$p_slug] = $p_name;
	}
	$currenttermid = $terms[$current];
	foreach((array)$terms as $term => $termid){
		if(in_array($term,$related))
		$results = mysql_query("SELECT COUNT(*) AS c FROM wp_term_relationships a, wp_term_relationships b WHERE a.object_id = b.object_id AND a.term_taxonomy_id = $termid AND b.term_taxonomy_id = $currenttermid ");
		while ($row = mysql_fetch_assoc($results)){
			extract($row, EXTR_PREFIX_ALL, "p");
			if($p_c>0)$counts[$term] = $p_c;
		}
	}
	if(is_array($counts))ksort($counts);
	return array($counts,$term_names);
}
function length_sort($val1, $val2){//words then chars
	$retVal = 0;
	$firstVal = strlen($val1);
	$secondVal = strlen($val2);
	$firstWrds = substr_count($val1,' ');
	$secondWrds = substr_count($val2,' ');
	if($firstWrds > $secondWrds)$retVal = -1;
	elseif($firstWrds < $secondWrds)$retVal = 1;
	elseif($firstVal > $secondVal)$retVal = -1;
	elseif($firstVal < $secondVal)$retVal = 1;
	return $retVal;
}
function selective_dash($str){
	$strs = explode('+',$str);$ns = '';
	foreach($strs as $s){
		if(stristr($s,'0')||stristr($s,'1')||stristr($s,'2')||stristr($s,'5'))$ns .= ' '.str_replace(array('+'),array(" $in "),$s);
		else $ns .= ' '.str_replace(array('-','+'),array(' '," $in "),$s);
	}
	return trim($ns);
}

?>
