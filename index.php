<?php
$font    = "./sazanami-gothic.ttf";
$team_A  = $_GET['team_a']; //"AAA|0,9,0,0,10,21,3"
$team_B  = $_GET['team_b']; //"BBB|0,9,0,0,10,20,3"

if (!empty($team_A) && !empty($team_B)) {  
  $score_class = new ScoreImage;

  if (preg_match("/^[A-Za-z]+.\|[0-9x,]+/",$team_A) && preg_match("/^[A-Za-z]+.\|[0-9x,]+/",$team_B)) {
    $score_class->create_score($font,$team_A,$team_B);
  } else {
    echo "パラメータが正しくありません";
    exit;
  }
  
}

class ScoreImage {

  function create_score ($font,$team_A,$team_B) {
    
    $team_A  = explode("|", $team_A);
    $score_A = explode(",", $team_A[1]);

    $team_B  = explode("|", $team_B);
    $score_B = explode(",", $team_B[1]);

    $cnt = count($score_A);

    $total_A  = 0;
    $total_B  = 0;
    
    if ($cnt >= 9) {
      $extn_flg = 1;

      foreach ($score_A as $val) {
        $total_A = $val + $total_A;
      }

      foreach ($score_B as $val) {
        $total_B = $val + $total_B;
      }
      
    } else {
      $extn_flg = 0;
      
      for ($i=0; $i<=8; $i++) {
        if (isset($score_A[$i])) {
          $total_A = $score_A[$i] + $total_A;
        } else {
          $score_A[$i] = "\ ";
        }

        if (isset($score_B[$i])) {
          $total_B = $score_B[$i] + $total_B;
        } else {
          $score_B[$i] = "\ ";
        }
      }
    }

    $score_A[] = $total_A;
    $score_B[] = $total_B;

    $line  = "-draw \"line 2,2 236,2\"";
    $line .= " -draw \"line 2,18 236,18\"";
    $line .= " -draw \"line 2,36 236,36\"";
    $line .= " -draw \"line 2,54 236,54\"";

    $line .= " -draw \"line 2,2 2,54\"";
    $line .= " -draw \"line 237,2 237,54\"";
  
    $line .= " -draw \"line 35,2 35,54\"";

    $score_txt = "-annotate +6+16 Team ";
    $score_txt .= "-annotate +8+32 {$team_A[0]} "; //チーム名
    $score_txt .= "-annotate +8+50 {$team_B[0]} "; //チーム名

    $num = count($score_A);

    //得点セル幅
    $cell = 180 / ($num - 1);
    
    for ($i=0; $i<=$num; $i++) {
      $score_title = $i + 1;
      $score_line_x = 35 + ($cell * $score_title);
      
      $score_x = 23 + ($cell * $score_title);
      if ($i == ($num - 1)) {
        $score_txt .= "-annotate +{$score_x}+16 R ";
      } elseif($i < $num -1) {
        $line .= " -draw \"line {$score_line_x},2 {$score_line_x},54\"";
        $length = strlen($score_title);
        if ($length >= 2) $score_x = $score_x - 3;
        $score_txt .= "-annotate +{$score_x}+16 {$score_title} ";
      }

      $score_x = 23 + ($cell * $score_title);
      if (isset($score_A[$i])) {
        $length = strlen($score_A[$i]);
        if ($length >= 2) {
          $score_x = $score_x - 3;
        }
        
        if ($i == ($num- 1) && ($num >= 12) ) {
          $score_x = $score_x + 5;
        }
        
        $score_txt .= "-annotate +{$score_x}+32 {$score_A[$i]} ";
      }

      $score_x = 23 + ($cell * $score_title);
      if (isset($score_B[$i])) {
        $length = strlen($score_B[$i]);
        if ($length >= 2) {
          $score_x = $score_x - 3;
        }
        
        if ($i == ($num- 1) && ($num >= 12) ) {
          $score_x = $score_x + 5;
        }

        $score_txt .= "-annotate +{$score_x}+50 {$score_B[$i]} ";
      }
    }

    system("convert -size 240x58 xc:darkgreen -fill white {$line} -font {$font} -pointsize 12 {$score_txt} out.jpg");
    
    header('Content-type: image/jpeg');
    readfile('--OUT_PUT_PATH---/out.jpg');
  }
}
?>