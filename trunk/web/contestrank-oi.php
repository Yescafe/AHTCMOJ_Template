<?php
        $OJ_CACHE_SHARE=true;
        $cache_time=10;
        require_once('./include/cache_start.php');
    require_once('./include/db_info.inc.php');
        require_once('./include/setlang.php');
        $view_title= $MSG_CONTEST.$MSG_RANKLIST;
        $title="";
        require_once("./include/const.inc.php");
        require_once("./include/my_func.inc.php");
class TM{
        var $solved=0;
        var $time=0;
        var $p_wa_num;
        var $p_ac_sec;
        var $p_pass_rate;
        var $user_id;
        var $nick;
	var $total;
        function TM(){
                $this->solved=0;
                $this->time=0;
                $this->p_wa_num=array(0);
                $this->p_ac_sec=array(0);
                $this->p_pass_rate=array(0);
		$this->total=0;
        }
        function Add($pid,$sec,$res){
//              echo "Add $pid $sec $res<br>";
                if (isset($this->p_ac_sec[$pid])&&$this->p_ac_sec[$pid]>0)
                        return;
                if ($res*100<99){
                        if(isset($this->p_pass_rate[$pid])){
                                if($res>$this->p_pass_rate[$pid]){
					$this->total-=$this->p_pass_rate[$pid]*100;
					$this->p_pass_rate[$pid]=$res;
					$this->total+=$this->p_pass_rate[$pid]*100;
				}
                        }else{
                                $this->p_pass_rate[$pid]=$res;
				$this->total+=$res*100;
                        }
			if(isset($this->p_wa_num[$pid])){
	                        $this->p_wa_num[$pid]++;
        	        }else{
                	        $this->p_wa_num[$pid]=1;
                       	}

                }else{
                        $this->p_ac_sec[$pid]=$sec;
                        $this->solved++;
                        if(!isset($this->p_wa_num[$pid])) $this->p_wa_num[$pid]=0;
                        if(isset($this->p_pass_rate[$pid]))$this->total-=$this->p_pass_rate[$pid]*100;
			$this->total+=100;
			$this->time+=$sec+$this->p_wa_num[$pid]*1200;
//                      echo "Time:".$this->time."<br>";
//                      echo "Solved:".$this->solved."<br>";
                }
        }
}

function s_cmp($A,$B){
//      echo "Cmp....<br>";
        if ($A->solved!=$B->solved) return $A->solved<$B->solved;
        else {
		if($A->total!=$B->total)
			return $A->total<$B->total;
		else
			return $A->time>$B->time;
	}
}

// contest start time
if (!isset($_GET['cid'])) die("No Such Contest!");
$cid=intval($_GET['cid']);

$sql="SELECT `start_time`,`title`,`end_time` FROM `contest` WHERE `contest_id`='$cid'";

if($OJ_MEMCACHE){
        require("./include/memcache.php");
        $result = mysql_query_cache($sql);
        if($result) $rows_cnt=count($result);
        else $rows_cnt=0;
}else{

        $result = pdo_query($sql);
        if($result) $rows_cnt=count($result);
        else $rows_cnt=0;
}


$start_time=0;
$end_time=0;
if ($rows_cnt>0){
//       $row=$result[0];

        if($OJ_MEMCACHE)
                $row=$result[0];
        else
                 $row=$result[0];
        $start_time=strtotime($row['start_time']);
        $end_time=strtotime($row['end_time']);
        $title=$row['title'];
        
}
if ($start_time==0){
        $view_errors= "No Such Contest";
        require("template/".$OJ_TEMPLATE."/error.php");
        exit(0);
}

if ($start_time>time()){
        $view_errors= "Contest Not Started!";
        require("template/".$OJ_TEMPLATE."/error.php");
        exit(0);
}
if(!isset($OJ_RANK_LOCK_PERCENT)) 
$OJ_RANK_LOCK_PERCENT=1;
$lock=$end_time-($end_time-$start_time)*$OJ_RANK_LOCK_PERCENT;

//echo $lock.'-'.date("Y-m-d H:i:s",$lock);


$sql="SELECT count(1) as pbc FROM `contest_problem` WHERE `contest_id`='$cid'";
//$result=pdo_query($sql);
if($OJ_MEMCACHE){
//        require("./include/memcache.php");
        $result = mysql_query_cache($sql);
        if($result) $rows_cnt=count($result);
        else $rows_cnt=0;
}else{

        $result = pdo_query($sql);
        if($result) $rows_cnt=count($result);
        else $rows_cnt=0;
}

if($OJ_MEMCACHE)
        $row=$result[0];
else
         $row=$result[0];

// $row=$result[0];
$pid_cnt=intval($row['pbc']);

$sql="SELECT
        users.user_id,users.nick,solution.result,solution.num,solution.in_date,solution.pass_rate
                FROM
                        (select * from solution where solution.contest_id='$cid' and num>=0 and problem_id>0) solution
                left join users
                on users.user_id=solution.user_id
        ORDER BY users.user_id,in_date";
//echo $sql;
//$result=pdo_query($sql);
if($OJ_MEMCACHE){
   //     require("./include/memcache.php");
        $result = mysql_query_cache($sql);
        if($result) $rows_cnt=count($result);
        else $rows_cnt=0;
}else{

        $result = pdo_query($sql);
        if($result) $rows_cnt=count($result);
        else $rows_cnt=0;
}

$user_cnt=0;
$user_name='';
$U=array();
for ($i=0;$i<$rows_cnt;$i++){
        if($OJ_MEMCACHE)
                $row=$result[$i];
        else
                 $row=$result[0];

        $n_user=$row['user_id'];
        if (strcmp($user_name,$n_user)){
                $user_cnt++;
                $U[$user_cnt]=new TM();

                $U[$user_cnt]->user_id=$row['user_id'];
                $U[$user_cnt]->nick=$row['nick'];

                $user_name=$n_user;
        }
        if(time()<$end_time&&$lock<strtotime($row['in_date']))
        	   $U[$user_cnt]->Add($row['num'],strtotime($row['in_date'])-$start_time,0);
        else
        	   $U[$user_cnt]->Add($row['num'],strtotime($row['in_date'])-$start_time,$row['pass_rate']);
       
}
usort($U,"s_cmp");

////firstblood
$first_blood=array();
for($i=0;$i<$pid_cnt;$i++){
      $first_blood[$i]="";
}
$sql="select num,user_id from
        (select num,user_id from solution where contest_id=$cid and result=4 order by solution_id ) contest
        group by num";
if($OJ_MEMCACHE){
        $fb = mysql_query_cache($sql);
}else{
        $fb = pdo_query($sql);
}
foreach ($fb as $row){
         $first_blood[$row['num']]=$row['user_id'];
}



/////////////////////////Template
require("template/".$OJ_TEMPLATE."/contestrank-oi.php");
/////////////////////////Common foot
if(file_exists('./include/cache_end.php'))
        require_once('./include/cache_end.php');
?>
