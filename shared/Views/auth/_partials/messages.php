<?php
/* for failed login's redirect **/
if (isset($_GET['error']))
{
$errorMsg = $_GET['error'] ;
echo <<< _END
<script>
alert ("Error : $errorMsg");
</script>
_END;
}


/************/

/* default=empty */
$school_id = '';
$username = '';
if (isset($_GET['school_id']) && isset($_GET['username']))
{
$school_id = $_GET['school_id'] ;
$username = $_GET['username'] ;

echo <<< _END
<script>
alert ("Hello, $username! Account successfully created. Your School-ID is $school_id ");
</script>
_END;
}

/*************************************/

if(isset($_GET['logout']) && $_GET['logout'])
{
echo <<< _END
<script>
alert ("Logged out successfully.");
</script>
_END;
}



?>
