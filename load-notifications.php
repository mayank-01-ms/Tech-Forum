<?php

if ($loggedIn == true){
?>

<script>

$('.noti-no').hide();

function loadUnseenNotifications(read = ''){
    $(document).ready(function(){
        
        const action = 'load';

        $.ajax({
            url: '<?php echo $metaInfo['domain'];?>/fetch-notifications.php',
            type: 'post',
            data: {
                action: action,
                read: read
            },
            dataType: 'json',

            success: function(data){
                $('.notification-area').html(data.notifications);
                 
                if (data.unseen > 0){
                    $('.noti-no').show();
                    $('.noti-no').css('display', 'flex');
                    $('.noti-no').html(data.unseen);
                }
                if (data.unseen <= 0){
                    $('.noti-no').hide();
                    $('.noti-no').css('display', 'none');
                }
            }
        });
    });
}

loadUnseenNotifications();

$(document).ready(function(){
    $('.notification').click(function(){
        $('.noti-no').hide();
        setInterval(() => {            
            loadUnseenNotifications('read');
        }, 2000);
    });

    setInterval(function(){
        loadUnseenNotifications();
    }, 25000);
});
</script>

<?php
}
?>