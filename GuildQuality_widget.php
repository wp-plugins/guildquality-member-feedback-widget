<?php
/*
Plugin Name: GuildQuality Member Feedback Widget
Plugin URI: http://www.GuildQuality.com
Description: A simple widget that displays a Guildmember's feedback. GuildQuality surveys on behalf of quality minded home builders, remodelers and contractors.
Version: 1.4
Author: GuildQuality
Author URI: http://www.GuildQuality.com
*/

global $linksAreEnabled;

register_activation_hook( __FILE__,  'guildquality_plugin_install' );

function guildquality_plugin_install() {}

class GuildQualityWidget extends WP_Widget {
  function GuildQualityWidget() {
    $widget_ops = array('description' => 'Display recent published comments and reviews from your GuildQuality account on your WordPress site.' );
    parent::WP_Widget( false, $name = 'GuildQuality Member Feedback Widget', $widget_options = $widget_ops);
  }

  function widget( $args, $instance ) {


    extract( $args );
    $show_avatar    = esc_attr( $instance['show_avatar'] );
    $show_map       = esc_attr( $instance['show_map'] );
    $profileurl     = (esc_attr($instance['url'])!="") ? $this->fixurl($instance['url']) : "http://www.guildquality.com/";
    $response_type  = esc_attr( $instance['response_type'] );
    $quantity       = esc_attr( $instance['quantity'] );
    $bgColor        = esc_attr( $instance['bgColor'] );
    $txtColor       = esc_attr( $instance['txtColor'] );
    $linkColor      = esc_attr( $instance['linkColor'] );
    $disable_links  = false;// WP compliance

    //syntacticly speaking...
    $disabled = $disable_links;
    if($disabled == 1 || $disabled == "1"){
      $linksAreEnabled = false;
    } else {
      $linksAreEnabled = true;
    }

    ?>

    <?php
  echo $before_widget;
    ?>

    <style>
      .gq-star-img {
        background-image: url("<?= plugin_dir_url(__FILE__) ?>assets/gfx-wpstar-revised.png");
        background-repeat: no-repeat;
        float:none;
        margin:2px auto;
        padding:0;
        height:17px;
        width:80px;
      }
    </style>

    <?php
    // widget content
    $url = "http://www.guildquality.com/ws/wpwidget.php?mp=".$profileurl;
    switch ($instance['response_type']){
      case 'r':
        $only_comments = false;
        $only_reviews = true;
        break;
      case 'c':
        $only_comments = true;
        $only_reviews = false;
        break;
      default:
        $only_comments = false;
        $only_reviews = false;
        break;
    }

    if($only_reviews){
      $url .= "&only_reviews=1";
    } else if($only_comments){ // only_reviews trumps only_comments serverside
      $url .= "&only_comments=1";
    }
    $url_get_contents_return = wp_remote_get($url);// using wp transports API
    $wp_response_code = wp_remote_retrieve_response_code( $url_get_contents_return );
    $json = wp_remote_retrieve_body($url_get_contents_return);
    $data = json_decode($json, TRUE); // second param TRUE would return array instead of object

    /*
    <script type="text/javascript">
      var json_response = <?=$json?>; // this is for debugging only, PLEASE REMOVE BEFORE GOING TO PRODUCTION
    </script>
    */

    ?>
    <div class="gq-widget-outer" style="padding:10px;text-align:center;<?= ($bgColor!='') ? 'background-color:'.$bgColor.';':''; ?><?= 'color:'.$txtColor.';'; ?>">
      <?php
      if(isset($data['Name'])){ //verify request was a success
        echo ($linksAreEnabled) ? "<a href='".$profileurl."?WPwidget' style='color:".$linkColor."' title='".$data["Name"]." member profile on GuildQuality' target='_blank' >":"";
        if($instance['show_name']=="1"){
          echo "<b class='gq-name-text' style='float:none;font-size: 16px;margin-top: 11px;padding-right: 0px;'>".$data["Name"]."</b>";
        } else {?>
        <div class="gq-avatar-wrapper" style="width:100%;text-align:center;">
          <img class="gq-avatar" src= "http://www.guildquality.com/Guildquality.gif?k=<?= $data['GQBadge'] ?>.65" style="max-width: 95%"/>
        </div>
        <?php
        }
        echo ($linksAreEnabled) ? "</a>":"";
        if(isset($data['Map']) && $instance['show_map']=="1"){
          echo "<iframe src='".$data["Map"]."' width='272' height='280' style='padding-top:10px;float:right;'></iframe><br>";
        }
        ?>
        <!-- <div class="reviews" style="float:left;<?= ($instance['show_map']=='1')?'max-width:540px;':'' ?>"> -->
        <div class='gq-reviews' style="margin: 0 auto;max-width: 400px;">
        <?php

        echo "<span class='gq-blurb'>What our ".$instance['customer']."s say</span><hr class='gq-hr' style='background-color:#ccc;display:block;border:0;height:1px;margin:10px 0;padding:0'>";

        $svy = $data["Feedback"];
        $all = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19);
        if(count($svy) < 20) $all = array_slice($all, 0, count($svy));
        $solutionset = array();
        $workingset = $all;

        for ($i = 0; $i < $instance['quantity']; $i++){
          $which = rand(0,count($workingset)-1);
          $solutionset[count($solutionset)+1] = $workingset[$which];
          unset($workingset[$which]);
          $workingset = array_values($workingset);
        }
        $solved = sort($solutionset);

        // feedback display loop
        $count = 0;
        foreach($solutionset as $s){
          $count++;
          if($count <= $instance['quantity']){
            switch ($svy[$s]["type"]){
              case "review":
                ?>
                <?= ($linksAreEnabled) ? "<a href='http://www.guildquality.com/review/".$svy[$s]['id']."?WPwidget' style='color:".$linkColor."' title='".$data["Name"]." ".$instance["customer"]." review on GuildQuality' target='_blank'>":"";?>
                <div class="gq-review-title-wrapper" style="float:none;padding:0;margin:0;">
                  <b class="gq-review-title" style="padding:5px"><?= $svy[$s]["reviewer"] ?> in <?= ($svy[$s]["city"]!="")? $svy[$s]["city"].', '.$svy[$s]["state"]:$svy[$s]["state"]; ?></b>
                </div>
                <div class="gq-star-img" style="background-position: 0 <?= -19*($svy[$s]["rating"]) ?>px"></div>
                <?= ($linksAreEnabled) ? "</a>":""; ?>
                <div class="gq-review-content" style="text-align:left;width:100%;padding-top:10px">
                  <?= $svy[$s]["review"] ?>
                </div>
                <?php
                break;
              case "comment":
                ?>
                <?= ($linksAreEnabled) ? "<a href='http://www.guildquality.com/comment/".$svy[$s]['id']."?WPwidget' style='color:".$linkColor."' title='".$data["Name"]." ".$instance["customer"]." comment on GuildQuality' target='_blank'>":"";?>
                <div class="gq-comment-title-wrapper" style="float:none">
                  <p class="gq-comment-customer" style="padding: 5px 0 0 0;margin:0">From a <?= $instance["customer"]; ?> in <?= ($svy[$s]["city"]!="")? $svy[$s]["city"].', '.$svy[$s]["state"]:$svy[$s]["state"]; ?></p>
                  <b class="gq-comment-title" style="padding: 0 0 5px 0;margin:0"><?= $svy[$s]["question"] ?></b>
                </div>
                <?= ($linksAreEnabled) ? "</a>":""; ?>
                <div class="gq-comment-content" style="text-align:left;width:100%;padding-top:10px">
                  <?= $svy[$s]["comment"] ?>
                </div>
                <?php
                break;
            }
            if($svy[$s]['type'] == 'review' || $svy[$s]['type'] == 'comment'){
              echo "<hr class='gq-hr' style='background-color:#ccc;display:block;border:0;height:1px;margin:10px 0;padding:0'>";
            }
          }
        }

        ?>
        </div><!-- end .reviews div -->

        <br clear="both">
        <?php if($linksAreEnabled){ ?>
          <a class='gq-profile-link' href="<?= $profileurl ?>?WPwidget" style="color:<?=$linkColor?>" title="<?= $data["Name"] ?> member profile on GuildQuality" target="_blank"><p>View all feedback from <?= $data["Name"] ?></p></a>
        <?php } ?>
        <?php
        if($instance['show_name']=="1"){ ?>
          <div class='gq-icon-wrapper' style="width:100%;text-align:center;">
            <?= ($linksAreEnabled) ? "<a href='".$profileurl."?WPwidget' style='color:".$linkColor."' title='".$data["Name"]." member profile on GuildQuality' target='_blank' >":""; ?>
            <img class='gq-icon' src= "http://www.guildquality.com/Guildquality.gif?k=<?= $data['GQBadge'] ?>.65" style="max-width: 95%"/>
            <?= ($linksAreEnabled) ? "</a>":""; ?>
          </div>
        <?php
        } // end include logo check ?>
      <?php
      } else { //end json receipt validation
        echo "Unable to find GuildQuality account. Please verify that the <a href='".$profileurl."' target='_blank'>profile url</a> is valid.<br><br/>Response code: ".$wp_response_code;
      } ?>
    </div><!-- /.gq-widget-outer -->
    <?php

    // end widget content

    echo $after_widget;
  }

    /* Handle mistakes in the URL */
  private function fixurl( $url_in )
  {
      $url = $url_in;
      $noTrailing = (preg_match("/\/$/", $url) == 1) ? substr($url, 0, -1) : $url;// remove trailing slash (we will add it back later)
      // Determine link type and rebuild
      if(preg_match("/\/cr\//", $url)){ // old style links
        $components = preg_split("/\/cr\//", $noTrailing);
        $afterCR = $components[count($components)-1];
        $result = "http://www.guildquality.com/cr/".$afterCR;
      } else { // vanity URLs
        $components = preg_split("/\//", $noTrailing);
        $afterslash = $components[count($components)-1];
        $result = "http://www.guildquality.com/".$afterslash;
      }
      $result = $result."/";// reinclude trailing slash
      return $result;
    }

  function update( $new_instance, $old_instance ) {
    return $new_instance;
  }

  function form( $instance ) {
    $show_avatar = esc_attr( $instance['show_avatar'] );
    $show_name = esc_attr( $instance['show_name'] );
    $title = esc_attr( $instance['title'] );
    $url = esc_attr( $instance['url'] );
    $quantity = esc_attr( $instance['quantity'] );
    $customer = esc_attr( $instance['customer'] );
    $response_type = esc_attr( $instance['response_type'] );
    $type_choices = array(
      'Reviews & Comments'  => 'rc',
      'Reviews Only'        => 'r',
      'Comments Only'       => 'c'
    );
    $bgColor = esc_attr( $instance['bgColor'] );
    $txtColor = esc_attr( $instance['txtColor'] );
    $linkColor = esc_attr( $instance['linkColor'] );
    $disable_links = false;//esc_attr( $instance['disable_links'] );
    $qty_choices = array(
      '1' => 1,
      '2' => 2,
      '3' => 3,
      '4' => 4,
      '5' => 5,
      '6' => 6,
      '7' => 7,
      '8' => 8,
      '9' => 9,
      '10 (max)' => 10
    );
    $bg_choices = array(
      'white' => '#fff',
      'grey' => '#ededed',
      'transparent' => ''
    );
    $txt_choices = array(
      'black' => '#000',
      'white' => '#fff'
    );
    $link_choices = array(
      'orange' => '#DC660C',
      'blue' => '#2F64BF',
      'black' => '#000',
      'white' => '#fff'
    );
    $customer_choices = array(
      'customer' => 'customer',
      'client' => 'client',
      'homeowner' => 'homeowner',
      'member' => 'member',
      'resident' => 'resident'
    );


    ?>

    <div class="gq_settings">
      <div class="image-cont">
        <a href="http://www.guildquality.com/?WPwidgetAdmin" target="_blank">
          <img src= "<?= plugin_dir_url(__FILE__) ?>assets/gq_logo.png"/>
        </a>
      </div>
      <h4>Setup</h4>
      <p>
        <label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e( 'Account Public Profile URL:' ); ?>
        <input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" type="text" value="<?php echo $url; ?>" />
        </label>
      </p>
      <h4>Configure</h4>
      <table>
        <tr>
          <td height="29">
            <input id="<?php echo $this->get_field_id( 'show_name' ); ?>" type="checkbox"  name="<?php echo $this->get_field_name('show_name'); ?>" value="1" <?php echo $show_name == "1" ? 'checked="checked"' : ""; ?> />
          </td>
          <td>
            <label for="<?php echo $this->get_field_id( 'show_name' ); ?>">Display Member Name</label>
          </td>
        </tr>
      </table>


      <table width="100%" height="109" border="0">
        <tr>
          <td height="32"><label>Customer Alias: </label></td>
          <td>
          <?php
          echo '<select name="' . $this->get_field_name('customer') . '" id="' . $this->get_field_id('customer') . '">';
          foreach($customer_choices as $k => $v){
            echo '<option value="' . $v . '" ' . ($customer == $v ? 'selected="selected"' : "") .  '>' . $k . '</option>';
          }
          echo '</select>';
          ?>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <span>Display:&nbsp;&nbsp;</span>
            <?php
            echo '<select name="' . $this->get_field_name('response_type') . '" id="' . $this->get_field_id('response_type') . '">';
            foreach($type_choices as $k => $v){
              echo '<option value="' . $v . '" ' . ($response_type == $v ? 'selected="selected"' : "") .  '>' . $k . '</option>';
            }
            echo '</select>';
            ?>
          </td>
        </tr>
        <tr>
        <td height="32"><label>How many to display: </label></td>
        <td>
        <?php
        echo '<select name="' . $this->get_field_name('quantity') . '" id="' . $this->get_field_id('quantity') . '">';
        foreach($qty_choices as $k => $v){
          echo '<option value="' . $v . '" ' . ($quantity == $v ? 'selected="selected"' : "") .  '>' . $k . '</option>';
        }
        echo '</select>';
        ?>
        </td>
        </tr>
      </table>

      <h4>Color Scheme</h4>
      <table width="100%" height="109" border="0">
        <tr>
          <td height="32"><label>Background Color: </label></td>
          <td>
          <?php
          echo '<select name="' . $this->get_field_name('bgColor') . '" id="' . $this->get_field_id('bgColor') . '">';
          foreach($bg_choices as $k => $v){
            echo '<option value="' . $v . '" ' . ($bgColor == $v ? 'selected="selected"' : "") .  '>' . $k . '</option>';
          }
          echo '</select>';
          ?>
          </td>
        </tr>
        <tr>
          <td height="32"><label>Text Color: </label></td>
          <td>
          <?php
          echo '<select name="' . $this->get_field_name('txtColor') . '" id="' . $this->get_field_id('txtColor') . '">';
          foreach($txt_choices as $k => $v){
            echo '<option value="' . $v . '" ' . ($txtColor == $v ? 'selected="selected"' : "") .  '>' . $k . '</option>';
          }
          echo '</select>';
          ?>
          </td>
        </tr>
        <tr>
          <td height="32"><label>Link Color: </label></td>
          <td>
          <?php
          echo '<select name="' . $this->get_field_name('linkColor') . '" id="' . $this->get_field_id('linkColor') . '">';
          foreach($link_choices as $k => $v){
            echo '<option value="' . $v . '" ' . ($linkColor == $v ? 'selected="selected"' : "") .  '>' . $k . '</option>';
          }
          echo '</select>';
          ?>
          </td>
        </tr>
      </table>
      <h4></h4>
      <p>Questions? <a href="http://www.guildquality.com/blog/2012/10/16/wordpress-widget/" target="_blank">Check out this post</a> for instructions.</p>
    </div>
    <?php
  }
}

add_action( 'widgets_init', 'GuildQualityWidgetInit' );
function GuildQualityWidgetInit() {
  register_widget( 'GuildQualityWidget' );
}

function guildquality_widget_action() {}

add_action( 'wp_ajax_guildquality_widget_action', 'guildquality_widget_action' );
add_action( 'wp_ajax_nopriv_guildquality_widget_action', 'guildquality_widget_action' );

add_action( 'wp_head', 'guildquality_plugin_js_header' );

function guildquality_plugin_js_header() {
  ?>
   <script type="text/javascript">
     //<![CDATA[
     jQuery(document).ready(function($) {
       jQuery('#guildquality_text_submit_id').click(function(){
       jQuery.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', { 'action': 'guildquality_widget_action',  'user_text': jQuery('#guildquality_text_id').val() }  );
       });
     });
     //]]>
   </script>
  <?php
}

function gq_widget_scripts(){
  ?>
  <style type="text/css">
    .image-cont{
      width: 100%;
      text-align: center;
    }
    .gq_settings img{
      max-width: 95%;
    }
    .gq_settings h4{
      border-bottom: 1px solid #DFDFDF;
      margin: 20px -11px 10px -11px;
      padding: 5px 11px 5px 11px;
      border-top: 1px solid #DFDFDF;
      background-color: #fff;
      background-image: -ms-linear-gradient(top,#fff,#f9f9f9);
      background-image: -moz-linear-gradient(top,#fff,#f9f9f9);
      background-image: -o-linear-gradient(top,#fff,#f9f9f9);
      background-image: -webkit-gradient(linear,left top,left bottom,from(#fff),to(#f9f9f9));
      background-image: -webkit-linear-gradient(top,#fff,#f9f9f9);
      background-image: linear-gradient(top,#fff,#f9f9f9);
    }

    hr.gqWidget {
      background-color: #CCC;
      border: 0;
      height: 1px;
      margin-bottom: 10px;
      margin-top: 10px;
      padding: 0;
    }
  </style>

  <?php
}
add_action('admin_footer', 'gq_widget_scripts');

?>