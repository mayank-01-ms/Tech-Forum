<?php   
?>
    <script>

        function hamchange(){
            var hmc = document.getElementById('menu-btn').checked;
            return hmc;
        }
        $(document).ready(function(){
            $('.c-com').change(function(){
                $(".comments").slideToggle();
            });

            $(".share-btn").click(function(){
                $(".share-btn ul").slideToggle();
                $(".share-btn ul span").toggle();
            });

            $("#menu-btn").click(function(){                
                var hmc = hamchange();
                if (hmc == true){
                    $('nav').show();
                } else {
                    $('nav').hide();
                }
            });

            $(".notification").click(function(){
                $(".notification-area").slideToggle();
            });
            
        });
    </script>