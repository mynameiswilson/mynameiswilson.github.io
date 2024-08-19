<? 


require("rss.include.php");

class phpFeed extends Rss
  {
    var $config_file;
    var $xmlConfig;
    var $debug_on = 0;

    //initialize the arrays that will hold the information

    //note that the configuration options that are valid within the config
    //file are initialized here 
    var $newsFeedConfigArray = array(
         "feeddirectory" => "",
         "headerHTML" => "",
         "itemHTML" => "",
         "footerHTML" => "",
         "styleHTML" => "",
         "showimage" => "",
         "showtitle" => "",
         "showdesc" => "",
         "otfretrieve" => "",
         "cronretrieve" => ""
       );    

    //newsFeedsArray will be an array of newsFeeds from the config file
    var $newsFeedsArray = array();

    //again, note that the configuration options that are valid in the config
    //file are initialized here, so if you wanna add a configuration option to the
    //config file, just add an item into this array, and make sure that the XML
    //tag you've added to the config file is the same name (and case) as the item
    //you've added to the array.
    //NOTE: this array is not to be written to, only copied -- like so:
    //$tmparray = $this->newsFeedChildrenArray, 'cause then that $tmparray will
    //get pushed into $this->newsFeedsArray -- capice?    
    var $newsFeedChildrenArray = array(
      "url" => "",
      "filename" => "",
      "interval" => "",
      "timeunit" => "",  
      "showtitle" => "",
      "showimage" => "",
      "showdesc" => "",
      "showinput" => "",
      "showitemdesc" => "",
      "isactive" => "",
      "debug" => ""
    );              


    function phpFeed($config_file)
      {

        // if an alternate config file hasn't been specified, try to use the default
        // it's a good idea to specify a config_file, though, 'cause newsfeeds.inc.php is 
        // usually include()'ed into a file, and the path to the config file is usually different
        // so, try and use it.  it's better that way    
        if(!empty($config_file))
          $this->config_file = $config_file;
        else
          $this->config_file = "newsfeeds.config.xml";

        $this->xmlConfig = domxml_open_file($this->config_file);
        
        // if the config file was kosher, then get the data! 
        if($this->xmlConfig) 
          {
            //pass the root node (in our case, it's the <config> node) 
            //$this->getMainConfig($this->xmlConfig->document_element());
            //$this->getNewsFeeds($this->xmlConfig->document_element());
            $this->getMainConfig(domxml_root($this->xmlConfig));
            $this->getNewsFeeds(domxml_root($this->xmlConfig));
          }
        //otherwise bail out!
        else 
          {
            echo "config file: $this->config_file is not a valid xml file\n";
            exit();
          }

      }


   function getMainConfig($config_node)
     {
       //here is where the array setup at the top is critical.
       //for each array key, try and find that same tag in the XML file!
       //so, on the first loop, it tries to find the <showimages> tag
 
       foreach(array_keys($this->newsFeedConfigArray) as $array_key)
         {
           //use the get child function to find the node with name $array_key that
           //hangs of $config_node. in our case, find <showimages> that hangs from
           //<config> 

           //$tmpnode = $this->xml_get_child_by_name($config_node,$array_key);
           $tmpnode = $config_node->get_elements_by_tagname($array_key);
	   if ($tmpnode)
             {
               //if we've found that node, then set the key we are looking for 
               // in the config array to the content of the node! 
	       $tmpnode_child = domxml_children($tmpnode[0]);

               $this->debug("<br>found $array_key: ".$tmpnode_child[0]->content);
               $this->newsFeedConfigArray[$array_key] = $tmpnode_child[0]->content;
             } 
         }
     }


   //get feeds from the XML
   function getNewsFeeds($newsfeed_parent_node)
     {
       //find the children of the $newsfeed parent node! (in our case,
       //the parent node is <config>
       //$childs = $newsfeed_parent_node->child_nodes();
//	$childs = domxml_children($newsfeed_parent_node);      
	$childs = $newsfeed_parent_node->get_elements_by_tagname("newsfeed");

       //loop through each 
       //while(list($key,$node)=each($childs)) {
       foreach ($childs as $node) {

	 //if the node is named newsfeed
        if($node->tagname == "newsfeed") {
	  //debug and say we found one
           $this->debug("<br>found a newsfeed!");

           //make a copy of the newsFeedChildArray, which has the proper keys in it for a newsfeed
           $tmpNewsFeedChildArray = $this->newsFeedChildrenArray;

           //for each key in the newsFeedChildArray (essentially, each piece of a newsfeed)
           foreach(array_keys($this->newsFeedChildrenArray) as $array_key)
             {
               //try to find that node within the newsfeed node
               //$tmpnode = $this->xml_get_child_by_name($node,$array_key);
               $tmpnode = $node->get_elements_by_tagname($array_key);

               //if we've found it
               if ($tmpnode)
                 {
		   $tmpchild = domxml_children($tmpnode[0]);
                   //debug and say we have
                   $this->debug("<br>&nbsp;&nbsp;found $array_key: ".$tmpchild[0]->content);
                   //set the node within the temp newsFeedChildArray
                   $tmpNewsFeedChildArray[$array_key] = $tmpchild[0]->content; 
                 } 
             }
 
	   //push the newsfeed into the array of all newsfeeds
         array_push($this->newsFeedsArray,$tmpNewsFeedChildArray);
         }
       }  #end while
     }



  //returns true/false if the newsfeed file that exists is current, according to the 
  //interval/timeunit
   function NewsFeedFileIsCurrent($index)
	 {
           //get the necessary information from the config array
           $newsFeedFilename = $this->newsFeedConfigArray["feeddirectory"].$this->newsFeedsArray[$index]["filename"];
           $newsFeedInterval = $this->newsFeedsArray[$index]["interval"];
           $newsFeedTimeUnit = $this->newsFeedsArray[$index]["timeunit"];

           //if the interval is 
           switch (strtoupper($newsFeedTimeUnit)) 
             {
              case "D":
                $seconds = 86400;
              break;

	      case "H":
                $seconds = 3600;
              break;

              case "M":
                $seconds = 60;
              break;

              case "S":
                $seconds = 1;
              break;

              default:
	        $seconds = 3600;
		break;
             }

           $newsFeedGoodUntil = filemtime($newsFeedFilename) + ($newsFeedInterval * $seconds);


           if ($newsFeedGoodUntil <= time())
             {
               $this->debug("$newsFeedFilename is old!<br>");
               return 0;
             }
           else
             {
		$this->debug("$newsFeedFilename is good until $newsFeedGoodUntil (currently: ".time().")");
               return 1;
             }

	 }

   function GetFreshNewsFeed($index)
     { 
       $newsFeedFilename = $this->newsFeedConfigArray["feeddirectory"]."/".$this->newsFeedsArray[$index]["filename"];
       $newsFeedURL = $this->newsFeedsArray[$index]["url"];

       $this->debug("getting fresh feed for $newsFeedFilename from $newsFeedURL<br>");

       // get contents of a URL into a string
       if ($fd = fopen ($newsFeedURL, "r"))
         {
           $this->debug("Open for READ of $newsFeedURL Successful!<br>");
           $contents = fread ($fd,1000000);
           fclose ($fd);

           if($fd = fopen ($newsFeedFilename,"w"))
             {
               $this->debug("Open for WRITE of $newsFeedFilename Successful!<br>");
               fputs($fd,$contents);
               fclose ($fd);
               clearstatcache();
               return 1;
             }
           else
             {
                $this->debug("Put of $newsFeedFilename failed!<br>");
                return 0;
             }
         }
       else
         {
           $this->debug("Get of $newsFeedURL failed!<br>");
           return 0;
         }

     }

   function debug($text) 
     {
       if ($this->debug_on) { echo $text."\n"; }
     }



   function showNewsFeed($index,$filter)
     {
       $newsFeedFilename = $this->newsFeedConfigArray["feeddirectory"]."/".$this->newsFeedsArray[$index]["filename"];

       $this->debug("<P>START News Feed for $newsFeedFilename<br>");
       $this->debug("isactive ->".$this->newsFeedsArray[$index]["isactive"]."<br>empty?-->".sizeof($this->newsFeedsArray[$index]['isactive']));

         if($this->NewsFeedFileIsCurrent($index) || $this->newsFeedConfigArray["otfretrieve"] == "0" )
           {
             $this->debug("$newsFeedFilename is Current!<br>");
             $this->Rss($newsFeedFilename);
             $this->HTML($index,$filter);
           
           }
         else
           {
             if($this->newsFeedConfigArray["otfretrieve"] == "1") 
               {
               if($this->GetFreshNewsFeed($index) == 0)
                 {
                   $this->debug("<br>failed to get Fresh News Feed for $newsFeedFilename!");
                 }
               }

               $this->showNewsFeed($index,$filter);

           } 
      $this->debug("END $newsFeedFilename<br>");
     }


   function HTML($index,$filter)
     {
	$feedindex = $index;
	$filetimestamp = filemtime($this->newsFeedConfigArray["feeddirectory"]."/".$this->newsFeedsArray[$index]["filename"]);
	$filedate = date("d M Y @ h:i:s A",$filetimestamp);



	$channel_title = $channel_link = $item_desc = $title = $url = $link = "";
	$image_link = $image_title = $image_url = $channel_description = $stylefilename = "";
	$this->debug("showdesc?-->".$this->newsFeedsArray[$index]["showdesc"]);

	if($this->newsFeedsArray[$index]["debug"]) {echo "<pre>"; print_r($this->items); echo "</pre>";}

       	if($this->newsFeedsArray[$index]["showtitle"] != 0 
		|| sizeof($this->newsFeedsArray[$index]["showtitle"]) == 0)  
         { 
           $channel_title = $this->channel->title;
           $channel_link = $this->channel->link;
         }
       	
	if($this->newsFeedsArray[$index]["showdesc"] != 0 || 
		sizeof($this->newsFeedsArray[$index]["showdesc"]) == 0)  
	   $channel_description = $this->channel->desc;

       	if($this->newsFeedsArray[$index]["showimage"] != 0 || 
		sizeof($this->newsFeedsArray[$index]["showimage"]) == 0)  
         { 
           $image_link = $this->image->link;
           $image_url  = $this->image->url;
           $image_title = $this->image->title;
         }


       	if (!empty($this->newsFeedConfigArray["styleHTML"]))
         {
           $stylefilename = $this->newsFeedConfigArray["styleHTML"];
         }

       	if (!empty($this->newsFeedConfigArray["headerHTML"]))
         {
           include($this->newsFeedConfigArray["headerHTML"]);
         }

       	for($i=0;$i<$this->item_count;$i++) 
       	{
	  if ($filter != "") {

	    if ( strstr(strtolower($this->items[$i]->title),strtolower($filter)) != FALSE 
	         ||
		 strstr(strtolower($this->items[$i]->desc),strtolower($filter)) != FALSE
	    ) {
	      $this->showHTMLItem($i);
	    }

	  } else {
	    $this->showHTMLItem($i);
	  }
	}
       	include($this->newsFeedConfigArray["footerHTML"]);

     }

   function showHTMLItem($i) {
         $item_title = $this->items[$i]->title;
         $item_link = $this->items[$i]->link;
         if($this->newsFeedsArray[$index]["showitemdesc"] != 0 or sizeof($this->newsFeedsArray[$index]["showitemdesc"])==0) 
           $item_desc = $this->items[$i]->desc;
         include($this->newsFeedConfigArray["itemHTML"]);
         $item_title = "";
         $item_link = "";
         $item_desc = "";
   }


   function showNewsFeedsByKeyword($filter)
     {
       foreach($this->ArrayActiveIndex() as $i)
         $this->showNewsFeed($i,$filter);
     
     }

   function showAllActiveNewsFeeds()
     {
       foreach($this->ArrayActiveIndex() as $i)
         $this->showNewsFeed($i,"");
     }

   function showAllNewsFeeds()
     {
       for($i=0;$i<$this->NewsFeedsCount();$i++)
         $this->showNewsFeed($i,"");
     }

   function NewsFeedsCount()
     {
       return sizeof($this->newsFeedsArray);
     }

   function ActiveNewsFeedsCount()
     {
	$x = 0;
	foreach ($this->newsFeedsArray as $i)
		{
		if ( ( $i["isactive"] == 1 ) || ( $i["isactive"] == "1" ) ) { $x++; }  
		}
	return $x;

     }
	
   function ArrayActiveIndex()
	{
	$activearray = array();
	$x = 0;
	foreach($this->newsFeedsArray as $i)
		{
		if ( ( $i["isactive"] == 1 ) || ( $i["isactive"] == "1" ) ) 
			{ 
			array_push($activearray,$x);
			}
		$x++;
		}


	return $activearray;
	}


}
?>
