<?php
	// Functions file for the plugin Extended Useradmininfos
	// © 2013-2019 Florian Berger
	// Last change: 2019-11-02

function getBrowser($u_agent)
{
    $bname = '';
    $platform = '';

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'Linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'Mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        if (preg_match('/NT 5.0/i', $u_agent)) {
			$platform = 'Windows 2000';
		} elseif (preg_match('/NT 5.1/i', $u_agent)) {
			$platform = 'Windows XP';
		} elseif (preg_match('/NT 6.0/i', $u_agent)) {
			$platform = 'Windows Vista';
		} elseif (preg_match('/NT 6.1/i', $u_agent)) {
			$platform = 'Windows 7';
		} elseif (preg_match('/NT 6.2/i', $u_agent)) {
			$platform = 'Windows 8';
		} elseif (preg_match('/NT 6.3/i', $u_agent)) {
			$platform = 'Windows 8.1';
		} elseif (preg_match('/NT 10.0/i', $u_agent)) {
			$platform = 'Windows 10';
		} else {
			$platform = 'Windows';
		}
    }
   
    // Next get the name of the useragent yes seperately and for good reason
    if (preg_match('/Edg/i', $u_agent)) {
        $bname = 'Microsoft Edge (Chromium)';
        $ub = "Edg";
    }
    elseif (preg_match('/Edge/i', $u_agent)) {
        $bname = 'Microsoft Edge';
        $ub = "Edge";
    }
    elseif (preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    elseif (preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }
    elseif (preg_match('/Chrome/i',$u_agent))
    {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }
    elseif (preg_match('/Safari/i',$u_agent))
    {
        $bname = 'Apple Safari';
        $ub = "Safari";
    }
    elseif (preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    }
    elseif (preg_match('/Netscape/i',$u_agent))
    {
        $bname = 'Netscape';
        $ub = "Netscape";
    }
   
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if(!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
   
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent, "Version") < strripos($u_agent, $ub)){
            $version = $matches['version'][0];
        }
        else {
            $version = $matches['version'][1];
        }
    }
    else {
        $version = $matches['version'][0];
    }
   
    // check if we have a number
    if ($version == null || $version == "") {$version = "?";}
   
    return array(
        'userAgent' => $u_agent,
        'browser'   => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}

?>