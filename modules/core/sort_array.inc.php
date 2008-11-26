<? 

/* EXAMPLE:
 
$sample_arr = array (
                        array   (
                                    "UserName"  => "Usr1",
                                    "Sex"       => "male",
                                    "Country"   => "US",
                                    "Children"  => 3,
                                    "BirthDate" => "1970-07-21"
                                ),
                        array   (
                                    "UserName"  => "TheUser",
                                    "Sex"       => "female",
                                    "Country"   => "Canada",
                                    "Children"  => 0,
                                    "BirthDate" => "1975-11-05"
                                )
                    );
 
$srt = new arr_multisort($sample_arr);
$srt->sort();
dump_arr($sample_arr);

//Sort by sex descending and children ascending
$srt->colNames = array("Sex",    "Children");
$srt->colDirs  = array(SRT_DESC,SRT_ASC);
$sample_arr = $srt->sort();
 
//Sort by sex ascending and Birth date decending
$srt->colNames = array("Sex",   "BirthDate");
$srt->colDirs  = array(SRT_ASC,SRT_DESC);
$sample_arr = $srt->sort();
 
*/


define("SRT_ASC",1);
define("SRT_DESC",-1);

Class arr_multisort{

  //The array to be sorted
  var $arr = NULL;
  //Single dimensioned array with column names. Ex. array("UserName","Sex","Country")
  var $colNames = NULL;
  /*
  Single dimensioned array with sort directions. Ex. array(SRT_ASC,SRT_ASC,SRT_DESC)
  Must have the same length as $colNames array
  */
  var $colDirs = NULL;

  //Constructor
  function arr_multisort(&$arr,$colNames=array(),$colDirs=array()){
    $this->arr = $arr;
    $this->colNames = $colNames;
    $this->colDirs = $colDirs;
  }

  //sort() method
  function &sort(){
    usort($this->arr,array($this,"_compare"));
    return $this->arr;
  }

  //Comparison function [PRIVATE]
  function _compare($a,$b,$idx = 0){
    if(count($this->colNames) == 0 || count($this->colNames) != count($this->colDirs)) return 0;
    $a_cmp = $a[$this->colNames[$idx]];
    $b_cmp = $b[$this->colNames[$idx]];
    $a_dt = strtotime($a_cmp);
    $b_dt = strtotime($b_cmp);
    if(($a_dt == -1) || ($b_dt == -1))
      $ret = $this->colDirs[$idx]*strnatcasecmp($a_cmp,$b_cmp);
    else{
      $ret = $this->colDirs[$idx]*(($a_dt > $b_dt)?1:(($a_dt < $b_dt)?-1:0));
    }
    if($ret == 0){
      if($idx < (count($this->colNames)-1))
        return $this->_compare($a,$b,$idx+1);
      else
        return $ret;
    }
    else
      return $ret;
  }
}
?>