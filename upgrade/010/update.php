<?php
// Add alliance and corp summary tables.
function update010()
{
	global $url, $header, $footer;
	//Checking if this Update already done
	if (CURRENT_DB_UPDATE < "010" )
	{
		$qry = new DBQuery();
		$sql = "CREATE TABLE IF NOT EXISTS `kb3_sum_pilot` (
		  `psm_plt_id` int(11) NOT NULL DEFAULT '0',
		  `psm_shp_id` int(3) NOT NULL DEFAULT '0',
		  `psm_kill_count` int(11) NOT NULL DEFAULT '0',
		  `psm_kill_isk` float NOT NULL DEFAULT '0',
		  `psm_loss_count` int(11) NOT NULL DEFAULT '0',
		  `psm_loss_isk` float NOT NULL DEFAULT '0',
		  PRIMARY KEY (`psm_plt_id`,`psm_shp_id`)
		) ENGINE=InnoDB";
		$qry->execute($sql);

		config::set("DBUpdate", "010");
		echo $header;
		echo "Update 010 completed.";
		echo $footer;
		die();
	}
}
