<?php 

require_once("commonFiles/header.php");
require_once("files/Classes/VideoPlayer.php");
require_once("files/Classes/VideoInfoSection.php");
require_once("files/Classes/CommentsClass.php");

$commentsClass= new CommentsClass($con);
if(!isset($_GET["Id"]) && !isset($_POST["postComment"]))  {
    echo "No URL passed on to page";
    exit();
}


$vidId="";
if(isset($_GET["Id"]))
{
    $vidId = $_GET["Id"];
}
if(isset($_POST["postComment"]))
{
    $vidId = $_POST["postComment"];
}






$video= new Video($con,$vidId);
$video->incrementViews();

?>

<div class="PageDiv"> 
    <div class="watchLeftColumn embed-responsive embed-responsive-16by9">
    <?php

        $videoPlayer= new VideoPlayer($video);
        echo $videoPlayer->create(true);

        $videoPlayer= new VideoInfoSection($con,$video,$loggedInUser);

    ?>

    </div>







    <div class= "suggestions">
        <?php
            $videoGrid= new VideoGrid($con);
            echo $videoGrid->create(null, "Suggestions", false,$loggedInUserName);
        ?>

    </div>
</div>

<div>
<?php
    echo $videoPlayer->create();

    echo "<form action='download.php' method='POST' >
    
    <button class='btn btn-danger' type='submit' value='$vidId' name='downloadButton'>Download</button>
    
</form>";

$query=$con->prepare("select * from Playlist where videoId=:vi and userName=:un");
$query->bindParam(":un",$loggedInUserName);
$query->bindParam(":vi",$vidId);
$query->execute();

$query1=$con->prepare("select * from Favoritelist where videoId=:vi and userName=:un");
        $query1->bindParam(":un",$loggedInUserName);
        $query1->bindParam(":vi",$vidId);
        $query1->execute();

if($query->rowCount()==0)
{
echo "<form action='playlist.php' method='GET' style='padding-top:10px' >
    
    <button class='btn btn-dark' type='submit' value='$vidId' name='AddtoPlaylist'>Add to Playlist</button>
    
</form>";
}
else if($query1->rowCount()==0)
{
    echo "<form action='favoritelist.php' method='GET' style='padding-top:10px' >
    
    <button class='btn btn-primary' type='submit' value='$vidId' name='AddtoFavList'>Add to Favorites</button>
    
</form>";
}
else
{
echo "<h3 class='display-6 text-primary' style='padding-top:10px'>Video Added to Favorites</h3>";
}

?> 

</div>


<?php
echo "<div style='padding-top:20px;' ><h2 class='display-6'>Comment Section</h2></div>";
if(isset($_GET["Id"]))

{
    $result=$commentsClass->getAllCommentsOfVideo($vidId);
    if($result=="")
    {
        echo "No Comments";
    }
    else
    {
        echo $result;
    }

}

if(isset($_POST["postComment"]))

{
    $commentsClass->postComment($loggedInUserName,$vidId,$_POST['comment']); 
    $result=$commentsClass->getAllCommentsOfVideo($vidId);
    if($result=="")
    {
        echo "No Comments";
    }
    else
    {
        echo $result;
       
    }
    header("location:watch.php?Id=$vidId");
}


?>
<?php    

if($loggedInUserName!="")
{

echo "    <div class='commentSection' style='margin-right:425px;'>

<form action='watch.php' method='POST' style='padding-top:20px' >
    <div class='input-group'>
    <input type='text' id='comment' name='comment' required placeholder='your comment' class='form-control' >
    <div class='input-group-append'>
    <button class='btn btn-primary' type='submit' onclick='myFunction1()' value='$vidId' name='postComment'>Post</button>
    </div>
    </div>
    </form>
</div>";
}

?>

<!-- <div class="container-fluid"> 
<div class="row"> 
    <div class="embed-responsive embed-responsive-16by9 col-md-9">
       
    <?php

        //$videoPlayer= new VideoPlayer($video);
        //echo $videoPlayer->create(true);

        //$videoPlayer= new VideoInfoSection($con,$video,$loggedInUser );

    ?>
  
   </div>

    <div class= "suggestions col-md-3">
        <?php
          //  $videoGrid= new VideoGrid($con, $loggedInUser);
            //echo $videoGrid->create(null, null, false);
        ?>
    </div>
    
</div>
<div>
<?php
    //echo $videoPlayer->create();
?> 
</div>
</div> -->

<?php require_once("commonFiles/footer.php")?>
    