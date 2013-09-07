<?php
/*************************************************************************
php easy :: pagination scripts set - Version Two
==========================================================================
Author:      php easy code, www.phpeasycode.com
Web Site:    http://www.phpeasycode.com
Contact:     webmaster@phpeasycode.com
*************************************************************************/
function paginate_two($reload, $page, $tpages, $adjacents) {
	
	$firstlabel = "&laquo;&nbsp;";
	$prevlabel  = "&lsaquo;&nbsp;";
	$nextlabel  = "&nbsp;&rsaquo;";
	$lastlabel  = "&nbsp;&raquo;";
	
	$out = "<div class=\"pagin\">\n";
	
	// first
	if($page>($adjacents+1)) {
		$out.= "<a href=\"" . $reload . "\">" . $firstlabel . "</a>\n";
	}
	else {
		$out.= "<span>" . $firstlabel . "</span>\n";
	}
	
	// previous
	if($page==1) {
		$out.= "<span>" . $prevlabel . "</span>\n";
	}
	elseif($page==2) {
		$out.= "<a href=\"" . $reload . ".php\">" . $prevlabel . "</a>\n";
	}
	else {
		$out.= "<a href=\"" . $reload . "/page/" . ($page-1) . ".php\">" . $prevlabel . "</a>\n";
	}
	
	// 1 2 3 4 etc
	$pmin = ($page>$adjacents) ? ($page-$adjacents) : 1;
	$pmax = ($page<($tpages-$adjacents)) ? ($page+$adjacents) : $tpages;
	for($i=$pmin; $i<=$pmax; $i++) {
		if($i==$page) {
			$out.= "<span class=\"current\">" . $i . "</span>\n";
		}
		elseif($i==1) {
			$out.= "<a href=\"" . $reload . ".php\">" . $i . "</a>\n";
		}
		else {
			$out.= "<a href=\"" . $reload . "/page/" . $i . ".php\">" . $i . "</a>\n";
		}
	}
	
	// next
	if($page<$tpages) {
		$out.= "<a href=\"" . $reload . "/page/" .($page+1) . ".php\">" . $nextlabel . "</a>\n";
	}
	else {
		$out.= "<span>" . $nextlabel . "</span>\n";
	}
	
	// last
	if($page<($tpages-$adjacents)) {
		$out.= "<a href=\"" . $reload . "/page/" . $tpages . ".php\">" . $lastlabel . "</a>\n";
	}
	else {
		$out.= "<span>" . $lastlabel . "</span>\n";
	}
	
	$out.= "</div>";
	
	return $out;
}
?>