<?php
defined('VERSION') or exit('Sorry, you are not allowed to do that!');

define('max_data_length',20);

function fetch_locale_data($maxid=0,$modified='') {
    global $db;

    $output='';
    $cond='';

    $maxid=(int) $maxid;
    $modified=(int) $modified;

    $modified=date("Y-m-d H:i:s",$modified);

    $cond="and (updated_at is not null and updated_at>'$modified') or (id>$maxid) ";

    $sql="select id,name,phone,address,lga,area,premium,assoc,landmark,gaddress,lat,lng,updated_at from locale where 1 $cond order by id asc limit 0,".max_data_length."";
    $result=$db->query($sql);

    if(!$result) {return $output;}

    $json=Array();
    while($row=$result->fetch_assoc()) {
        $json[]=$row;
    }

    return array('total'=>count($json),'data'=>$json);
}


function fetch_events_data($maxid=0,$modified=0) {
    global $db;

    $output='';
    $cond='';

    $maxid=_parse_int($maxid);
    $modified=_parse_int($modified);

    $cond="and (modified>$modified or id>$maxid) ";

//correct version
//$sql="select id,uid,title,date,time,location,description,photo1,photo2,photo3,video1,video2,video3,modified,status from app_events where 1 $cond order by id asc limit 0,".max_data_length."";

//consolidated
    $sql="select id,uid,title,date,time,location,description,video1,video2,video3,modified,status from app_events where 1 $cond order by id asc limit 0,".max_data_length."";
    $result=$db->query($sql);

    if(!$result) {return $output;}

    $json=Array();
    while($row=$result->fetch_assoc()) {
        $json[]=$row;
    }

    return array('total'=>count($json),'data'=>$json);
}



function fetch_comments_data($maxid=0,$modified=0) {
    global $db;

    $output='';
    $cond='';

    $maxid=_parse_int($maxid);
    $modified=_parse_int($modified);

    $cond="and (modified>$modified or id>$maxid) ";

    $sql="select id,uid,eid,title,message,modified,status from app_comments where 1 $cond order by id asc limit 0,".max_data_length."";
    $result=$db->query($sql);

    if(!$result) {return $output;}

    $json=Array();
    while($row=$result->fetch_assoc()) {
        $json[]=$row;
    }

    return array('total'=>count($json),'data'=>$json);
}


function fetch_subscribers_data($maxid=0,$modified=0) {
    global $db;

    $output='';
    $cond='';

    $maxid=_parse_int($maxid);
    $modified=_parse_int($modified);

    $cond="and (modified>$modified or id>$maxid) ";

    $sql="select id,uid,eid,modified,status from app_subscribers where 1 $cond order by id asc limit 0,".max_data_length."";
    $result=$db->query($sql);

    if(!$result) {return $output;}

    $json=Array();
    while($row=$result->fetch_assoc()) {
        $json[]=$row;
    }

    return array('total'=>count($json),'data'=>$json);
}

function fetch_books_data($maxid=0,$modified=0) {
    global $db;

    $path=base_url()."/uploads/";

    $output='';
    $cond='';

    $maxid=_parse_int($maxid);
    $modified=_parse_int($modified);

    $cond="and (modified>$modified or id>$maxid) ";
    $cond.=" and (pdf!='' or mp3!='' or mp4!='') ";

    $sql="select id,name,phone,email,title,sss,cat,summary,jpg,pdf,mp3,mp4,zip,modified from app_books where 1 $cond order by id asc limit 0,".max_data_length."";
    $result=$db->query($sql);

    if(!$result) {return $output;}

    $json=Array();
    while($row=$result->fetch_assoc()) {

        foreach(array('jpg','pdf','mp3','mp4','zip') as $field) {
            if($row["$field"]!=null) {
                $row["$field"]=$path.$row["$field"];
            }
        }

//base_url()

        $json[]=$row;
    }

    return array('total'=>count($json),'data'=>$json);
}

function fetch_users_data($maxid=0,$modified=0) {
    global $db;

    $output='';
    $cond='';

    $maxid=_parse_int($maxid);
    $modified=_parse_int($modified);

    $cond="and (modified>$modified or id>$maxid) ";

    $sql="select id,fullname name,status from app_users where 1 $cond order by id asc limit 0,".max_data_length."";
    $result=$db->query($sql);

    if(!$result) {return $output;}

    $json=Array();
    while($row=$result->fetch_assoc()) {
        $json[]=$row;
    }

    return array('total'=>count($json),'data'=>$json);
}

function _parse_int($data=0) {
    $data=(int) $data;

    if(empty($data)) {$data='0';}
    return $data;
}