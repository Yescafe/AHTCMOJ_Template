<?php if(file_exists("include/db_info.inc.php")){
		require_once("include/db_info.inc.php");
	if(isset($OJ_LANG)){
		require_once("./lang/$OJ_LANG.php");
	}
}
$judge_result=Array($MSG_Pending,$MSG_Pending_Rejudging,$MSG_Compiling,$MSG_Running_Judging,$MSG_Accepted,$MSG_Presentation_Error,$MSG_Wrong_Answer,$MSG_Time_Limit_Exceed,$MSG_Memory_Limit_Exceed,$MSG_Output_Limit_Exceed,$MSG_Runtime_Error,$MSG_Compile_Error,$MSG_Compile_OK,$MSG_TEST_RUN);
$jresult=Array($MSG_PD,$MSG_PR,$MSG_CI,$MSG_RJ,$MSG_AC,$MSG_PE,$MSG_WA,$MSG_TLE,$MSG_MLE,$MSG_OLE,$MSG_RE,$MSG_CE,$MSG_CO,$MSG_TR);
$judge_color=Array("gray","gray","orange","orange","green","red","red","red","red","red","red","navy ","navy");
$language_enabled=Array(1, 1, 1, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0);
$language_name=Array("C","C++","Pascal","Java","Ruby","Bash","Python","PHP","Perl","C#","Obj-C","FreeBasic","Scheme","Clang","Clang++","Lua","JavaScript","Go","SQL(sqlite3)","Fortran","Matlab(Octave)","Rust","Haskell","Other Language");
$language_ext=Array( "c", "cc", "pas", "java", "rb", "sh", "py", "php","pl", "cs","m","bas","scm","c","cc","lua","js","go","sql","f95", "m", "rs", "hs");
$PID="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
$ball_color=Array('#66cccc','red','green','pink','yellow','violet','magenta','maroon','olive','chocolate');
$ball_name=Array('蒂芙妮蓝','红','green','pink','yellow','violet','magenta','maroon','olive','chocolate');
$color_theme=["default","primary","success","info","warning","danger"];
?>
