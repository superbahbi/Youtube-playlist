<? include './src/header.php'; ?>
<? include './src/echonest.class.php'; ?>
<? include './src/youtube.class.php'; ?>
    <div class="container">
      <div class="header">
        <ul class="nav nav-pills pull-right">
          <li>
          <form class="nav navbar-form navbar-left" role="search">
            <div class="form-group">
              <input type="text" class="form-control" placeholder="Search" name="search">
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
          </form>
          </li>
        </ul>
        <h3 class="text-muted">Youtube Playlist</h3>
      </div>
      <?
      if(isset($_GET['search']) || isset($_GET['id']) ){
          if(isset($_GET['search'])){
        	$en = new echonest($_GET['search'], 0, 50);
          } else if(isset($_GET['id'])) {
            $en = new echonest($_GET['id'], 1 ,50);
          } else {
              break;
          }
            $isOkay = true; 	
      }
      ?>
<?if($isOkay):?>
      <!-- Profile -->
      <div class="jumbotron">
        <h2><? echo $en->getName()?></h2>
        <img src="<?echo $en->getImage()?>" height="128">
        <p class=""> Genre : <?echo $en->getGenre()?> 
        </p>
        <p class=""> Since : <? echo $en->getActiveYears()?> </p>
        <p class=""> Location : <? echo $en->getLocation()?> </p>
        <p class=""><a href="<? echo $en->getUrl()?>"><? echo $en->getUrl()?></a> </p>
      </div>
      <!-- End of Profile -->
      
      <!-- Player -->
        <? 
        $yt = new youtube($en->getVideo(), $en->getName());
        ?>
        
      <div class="jumbotron">
        <h3><span id="CurrentPlaying" ></span></h3>
        <div id="player" class="video-container"></div>
        <?
            echo '<table class="table table-hover list">';
            echo '<tbody>';
            for($i = 0; $i < $yt->getNumVid(); $i++){
                
                echo '<tr id="'.$yt->getID($i).'";  href="javascript:void(0);" onclick="nextbybtn(\''.$yt->getID($i).'\',\''.$i.'\')">';
                    echo '<td>'.$yt->getTitle($i).'</td>';
                    echo '<td><i class="glyphicon glyphicon-eye-open"></i> '.$yt->getViewCount($i).'</td>';
                    echo '<td><i class="glyphicon glyphicon-thumbs-up"></i> '.$yt->getLikeCount($i).'</td>';
                    echo '<td><i class="glyphicon glyphicon-thumbs-down"></i> '.$yt->getDislikeCount($i).'</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        ?>
      </div>
      <!-- End of Player -->

    <!-- Review -->
        <div class="jumbotron">
            <h2>Review</h2>
            <div class="row marketing">
                <div class="col-lg-6">
                  <?
                     for($i = 0; $i < $en->getReviewNum(); $i++){
                         if($i == round($en->getReviewNum()/2)){
                             echo '</div>';
                             echo '<div class="col-lg-6">';
                         }
                        echo '<h4>'.$en->getReview($i).'</h4>';
                        echo '<p>'.$en->getReviewDate($i).' '.$en->getReviewSummary($i).'<a href='.$en->getReviewUrl($i).'>Read more..</a></p>';
                     }
                  ?>
                </div>	
            </div>
        </div>
    <!-- End of review -->
    <!-- Similar -->
        <div class="jumbotron">
            <h2>Similar</h2>
                <?
                     foreach($en->getSimilar() as $s){
                        echo '<a href=?id='.$s->id.'>'.$s->name.'</a> ';
                     }
                ?>
        </div>
    <!-- End of similar -->
<?php endif; ?>

    
<? 
    include './src/youtube.player.php';
    include './src/footer.php'; 
?>