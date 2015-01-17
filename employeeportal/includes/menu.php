 <div class="list-group">
   <a href="revenuemanager.php" class="list-group-item"><h4 class="list-group-item-heading">Home</h4></a>
     
        <?php do { ?>
          <a href="centerdetails.php?centerID=<?php echo $row_Locations['centerID']; ?>" class="list-group-item ">
            <h4 class="list-group-item-heading"> <?php echo $row_Locations['locationname']; ?></h4>
            <p class="list-group-item-text"><?php echo $row_Locations['locationaddress']; ?></p>
            </a>
          <?php } while ($row_Locations = mysql_fetch_assoc($Locations)); ?>
       
     
  </div>