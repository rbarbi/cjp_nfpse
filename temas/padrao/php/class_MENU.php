<?php
/** 
@name   Class Drop Down Menu
@author Marcos thiago <fabismt@yahoo.com.br>
@date   22/04/2004
**/

class MENU {
///////////////////////////////////////////////////////////////////////////
/* MENU: Class Constructor. */    
  function MENU(){

    $this->menu                         = array();

    $this->subMenu                      = array();
    $this->SubMenuIndex                 = 0;

    $this->menuName                     = "";
    $this->menuImg                      = "";
    $this->MenuIndex                    = 0;


    $this->styleMenuLinkFont            = "Verdana, Arial, Helvetica, sans-serif";
    $this->styleMenuLinkFontSize        = "10px";
    $this->styleMenuLinkFontColor       = "#666768";
    $this->styleMenuLinkTextDecoration  = "none";
    
    $this->styleMenuFont                = "Verdana, Arial, Helvetica, sans-serif";
    $this->styleMenuFontSize            = "10px";
    $this->styleMenuFontColor           = "#F4F4F4";
    $this->styleMenuBackgroundColor     = "#EDF1F4";
    $this->styleMenuBorderColor         = "#AFAFAF";
    $this->styleMenuBorderStyle         = "solid";
    $this->styleMenuBorderBottomWidth   = "1px";
    $this->styleMenuBorderLeftWidth     = "1px";
    $this->styleMenuBorderRightWidth    = "1px";
    $this->styleMenuBorderTopWidth      = "0px";
  
  }  
///////////////////////////////////////////////////////////////////////////
/* get: Retrieve value from a class variable */    
  function get($var){
    return $this->$var;
  }
///////////////////////////////////////////////////////////////////////////
/* set: Set value to a class variable */    
  function set($var, $value){
    $this->$var = $value;
  }
///////////////////////////////////////////////////////////////////////////
/* addMenuName: Set the menu name */    
  function addMenuName($name){
    $this->menuName = $name;
  }
///////////////////////////////////////////////////////////////////////////
/* addMenuImg: Set the menu image */    
  function addMenuImg($img_path){
    $this->menuImg = $img_path;
  }
///////////////////////////////////////////////////////////////////////////
/* isRepeated: Check for repeated menu names */  
  function isRepeated($name){
    foreach($this->menu as $each_menu){
      if($each_menu["NAME"] == $name)
          return TRUE;
    }
    return FALSE;
  }
///////////////////////////////////////////////////////////////////////////
/* addSubMenu: Insert the links */  
  function addSubMenu($name,$link,$target){
    $this->subMenu[$this->SubMenuIndex]["NAME"]     = $name;
    $this->subMenu[$this->SubMenuIndex]["LINK"]     = $link;
    $this->subMenu[$this->SubMenuIndex++]["TARGET"] = $target;
  }
//////////////////////////////////////////////////////////////////////////
/* buildMenu: Arrange the menu inside a array */  
  function buildMenu(){
    if(!$this->isRepeated("menu_".$this->menuName)){
      $this->menu[$this->MenuIndex]["ID"]        = $this->MenuIndex;
      $this->menu[$this->MenuIndex]["NAME"]      = "menu_".$this->menuName;
      $this->menu[$this->MenuIndex]["IMG"]       = $this->menuImg;
      $this->menu[$this->MenuIndex++]["SUBMENU"] = $this->subMenu;
      unset($this->subMenu);
    }
  }
///////////////////////////////////////////////////////////////////////////
/* generateMenuHtml: Generate menu style sheet */  
  function generateStyle(){
    print "
      <style>
      .menu {
        font-family: $this->styleMenuFont;
        font-size: $this->styleMenuFontSize; 
        color: $this->styleMenuFontColor; 
        background-color: $this->styleMenuBackgroundColor;
        border-color: $this->styleMenuBorderColor;
        border-style: $this->styleMenuBorderStyle;
        border-bottom-width: $this->styleMenuBorderBottomWidth;
        border-left-width: $this->styleMenuBorderLeftWidth;
        border-right-width: $this->styleMenuBorderRightWidth;
        border-top-width: $this->styleMenuBorderTopWidth;

      }
      .menulink { 
        font-family: $this->styleMenuLinkFont; 
        font-size: $this->styleMenuLinkFontSize;
        color: $this->styleMenuLinkFontColor;
        text-decoration: $this->styleMenuLinkTextDecoration;
      }
      </style>";
  }
///////////////////////////////////////////////////////////////////////////
/* generateScript: Generate menu javascript */
  function generateScript(){
    print "
      <script>
      function showMenu(item) { 
        var obj = document.getElementById(item);";
           
        foreach($this->menu as $each_menu){
          print "\n\t\t document.getElementById('{$each_menu["NAME"]}').style.display='none';";
        }

    print "
        if (obj.style.display == 'none'){
          obj.style.display='';
        } else {
          obj.style.display='none';
        }
      }
      </script>";
  }
///////////////////////////////////////////////////////////////////////////
/* generateMenuHtml: Generate menu html */  
  function generateMenuHtml($selected_menu = FALSE){

    print "
      <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
        <tr valign=\"top\"> 
          <td> 
            <table width=\"140\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
      ";
    $count = 0;
    foreach($this->menu as $each_menu){

      $img_id = $each_menu["NAME"]."_Img";

      // Dcidindo qual menu será aberto.
      if(!$selected_menu)
        $display = ($count != 0) ? "none" : "block";
      else
        $display = ($selected_menu != $each_menu["ID"]) ? "none" : $display = "block";

      print "
              <tr>
                <td> 
                  <div><img style=\"cursor:hand\" onClick=\"showMenu('{$each_menu["NAME"]}')\" src=\"".$each_menu["IMG"]."\" name=\"$img_id\" hspace=\"0\"></div>
                </td>
              </tr>
              <tr> 
                <td bordercolor=\"#FFFFFF\">
                  <span id=\"".$each_menu["NAME"]."\" style=\"display:$display\">
                  <table width=\"140\" cellpadding=\"0\" cellspacing=\"0\">";

      foreach($each_menu["SUBMENU"] as $each_submenu){  
        $each_submenu["LINK"] .= (eregi("\?", $each_submenu["LINK"])) ? "&" : "?" ;
        $each_submenu["LINK"] .= "selected_menu=".$each_menu["ID"];
        
        print "
                      <tr> 
                        <td height=\"22\" width=\"5\">&nbsp;</td>
                        <td height=\"22\" class=\"menu\" width=\"135\"><a href=\"".$each_submenu["LINK"]."\"  target=\"".$each_submenu["TARGET"]."\"class=\"menulink\">&nbsp;".$each_submenu["NAME"]."</a></td>
                      </tr>";
      }
      print "
                  </table>
                  </span> 
                </td>
              </tr>";
      $count++;
    }
      print "
            </table>
          </td>
        </tr>
      </table>";
  }
///////////////////////////////////////////////////////////////////////////
/* showMenu: Generate the output */  
  function showMenu($selected_menu){
    $this->generateStyle();
    $this->generateScript();
    $this->generateMenuHtml($selected_menu);
  }
///////////////////////////////////////////////////////////////////////////
/* This function exists just for test the class */
 function openHtmlTest(){
    print "
    <html>
    <head>
    <title>Drop Down Menu Sample</title>
    </head> 
    <body>
    <table width=\"100%\" border=\"0\">
      <tr><td style=\"font-family:Verdana, Arial; font-size:14px; color:#000000; font-weight: bold; text-align: center \">Drop Down Menu Example by Marcos Thiago - fabismt@yahoo.com.br</td></tr>
      <tr>
        <td style=\"font-family:Verdana, Arial; font-size:12px; color:#000000; text-align: center \">Click over the images to open the menu</td>
      </tr>
    </table>
    <table width=\"100%\" border=\"1\">
      <tr>
        <td style=\"width: 200px\">";
 }
///////////////////////////////////////////////////////////////////////////
/* This function exists just for test the class */
 function closeHtmlTest($arg = FALSE){
    print "
          </td>
        <!-- This place could be used to include a file. -->
        <td style=\"text-align: center;font-family: Verdana, Arial; font-size: 16px ;font-weight: bold\">Page $arg</td>
      </tr>
    </table>
    </body>
    </html>";
 }
///////////////////////////////////////////////////////////////////////////
}
?>