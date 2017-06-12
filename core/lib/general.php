<?php
$status_messages=array();

define('CD_FORMAT','Y/m/d H:i:s');

//define('matching_timer','+30 minutes');

//define('matching_timer','+2 minutes');

define('matching_timer','+10 hours');

$base_query="
select
s.id,
s.package_id pkid,
p.name pkname,
p.amount pkamount,
s.user_id,
s.sponsor_id,
s.time timer,
s.proof_time,
s.transaction_type trans,
s.amount_text amount,
s.status
from
sponsor s
left join packages p on p.id=s.package_id
";

$banks=array(
    '11'=>'Access Bank',
    '6'=>'Afri Bank',
    '21'=>'Citi Bank',
    '18'=>'Diamond Bank',
    '8'=>'Eco Bank',
    '13'=>'FCMB',
    '17'=>'Fidelity',
    '1'=>'First Bank',
    '4'=>'GTB',
    '14'=>'Heritage Bank',
    '15'=>'Keystone',
    '19'=>'Skye Bank',
    '20'=>'Stanbic IBTC Bank',
    '22'=>'Standard Chartered',
    '7'=>'Sterling Bank',
    '9'=>'UBA',
    '5'=>'Union Bank',
    '12'=>'Unity Bank',
    '10'=>'WEMA Bank',
    '16'=>'Zenith Bank',
);


function run_pending_tasks()
{
    $db;

}

/**
 * id field in the sponsor field
 * sid is userid of sponsor
 *
 */
function match_sponsor($id,$sid=null)
{
    global $db;

    if($sid==null) {
        $sponsor=$db->dlookup("*","sponsor","id=$id");
        $user_id=$sponsor['user_id'];

        $sql="select id from users where id!=$user_id and status=1 and blocked=0 order by hot asc limit 1";
        $row=$db->query($sql)->fetch_assoc();

        $sid=$row['id'];
    }

    $hot=time();


    $db->query("update sponsor set sponsor_id=$sid,status=1 where id=$id and sponsor_id=0");

    $db->query("update users set hot=$hot where id=$sid");

}


function show_sponsor_info($sponsor,$warning=true)
{
    ?>
    <div class="row">
        <div class="col-xs-7">

            <h2>Fullname: <b>
                    <?= $sponsor->first. ' ' . $sponsor->last ?><b></b></b></h2><b><b>
                    <p><strong>Account Name: </strong>
                        <?= $sponsor->acc_name_text ?> </p>
                    <p><strong>Bank: </strong>
                        <?php echo $GLOBALS['banks'][$sponsor->bank_text]; ?>  </p>
                    <p><strong>Account No.: </strong>
                        <?= $sponsor->acc_no_text ?> </p>
                    <p><strong>Donation Type: </strong> Cash or Transfer or Bank Payment </p>
                    <p><strong>Added Note: </strong> <?= $sponsor->description_text ?> </p>
                    <br>
                    <p><i class="fa fa-envelope user-profile-icon"></i> E-mail:
                        <?= $sponsor->email ?></p>
                    <p><i class="fa fa-phone"></i> Phone #:
                        <?= $sponsor->phone ?></p>
                    </ul>
                </b></b>

            <?php if(is_admin()) { ?>
                <a href="<?= site_url('dashboard/profile')."?id=".$sponsor->id ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit m-right-xs"></i> Manage This Profile</a>
            <?php } ?>

            <?php if($warning) {?>
                <div class="alert alert-danger">
                    You are to donate money to your Sponsor.
                    <span style="font-weight:800;">Discard any call or text from any sponsor forcing you to make payment. Please do not upload your payment details before paying. Its a criminal offence. </span>
                </div>
            <?php } ?>

        </div><b><b>
                <div class="right col-xs-5 text-center">
                    <div class="pull-right">
                        <img src="<?= get_avatar_image($sponsor) ?>" alt="" class="img-circle img-responsive">
                    </div>
                </div>
            </b></b>
    </div>
    <?php
}


function add_status_message($url,$text,$icon='fa fa-info') {
    global $status_messages;

    $status_messages[] = <<<end
<li> <a href="{$url}"><i class="{$icon}"></i> <span>
<span>{$text}</span>
<span class="time">now</span>
</span> </a>
</li>
end;

}

function show_downline_info($usr)
{
    ?>

    <div class="profile_img">
        <div id="crop-avatar">

            <img class="img-responsive avatar-view" style="" src="<?= get_avatar_image($usr) ?>" alt="Avatar">
        </div>
    </div>
    <h3><?= $usr->first. ' ' . $usr->last ?></h3>
    <ul class="list-unstyled user_data">
        <li class="m-top-xs"> <i class="fa fa-phone user-profile-icon"></i> <?= $usr->phone ?> </li>
        <li><i class="fa fa-envelope user-profile-icon"></i> <?= $usr->email ?> </li>
    </ul>
    <?php if(is_admin()) { ?>
    <a href="<?= site_url('dashboard/profile')."?id=".$usr->id ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit m-right-xs"></i> Manage This Profile</a>
<?php } ?>

    <?php
}


function status2text($status)
{

    switch($status) {
        case 0:
            return "Awaiting match";
            break;
        case 1:
            return "Waiting for pop";
            break;
        case 2:
            return "Confirm payment";
            break;
        case 3:
            return "Completed Transaction";
            break;
    }
    return $status;
}

/**
 * start match timer
 *
 */
function match_do_in($user_id,$pkid,$time=0)
{
    global $db;
    if($time==0) {$time=time();}
    $db->insert(
        'sponsor',
        array(
            'user_id'=>$user_id,
            'package_id'=>$pkid,
            'time'=>$time,
        )
    );
}

/**
 * Automatically match user
 *
 *
 */
function matching_do($package_id,$user_id,$limit=1)
{
    global $db;

    $sql="select id from users where id!=$user_id and status=1 and blocked=0 order by hot asc limit $limit";
    $users=$db->query($sql)->fetch_assoc_all();

    foreach($users as $user) {
        $data=array(
            'package_id'=>$package_id,
            'user_id'=>$user['id'],
            'sponsor_id'=>$user_id,
            'time'=>time(),
            'status'=>1,
        );
        $db->insert("sponsor",$data);
        $db->query("update users set hot=? where id=?",array(time(),$user['id']));
    }
}

/**
 * Deferred matching
 *
 *
 */
function matching_defer($package_id,$user_id,$limit=1,$time=0)
{
    global $db;
    if($time==0) {$time=strtotime('+5 days');}
    $data=array(
        'package_id'=>$package_id,
        'user_id'=>$user_id,
        'total'=>$limit,
        'time'=>$time,
        'status'=>0,
    );
    $db->insert("defer",$data);
}


function load_user_data($id, $admin = false) {
    global $db;

    $user = $db->dlookup('*','users',' id =  ?',array( $id ) );
    if (isset($user) && $admin) {
        $user = $user['admin'] == '1' ? $user : false;
    }

    return isset($user) && $user !== false ? (object) $user : false;
}