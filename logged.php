<img src="./img/benutzerbild.png" onclick="document.getElementById('loginall').style.display='block'" id="benutzerbild">
<a href="index.php"><img src="./img/logo.png" alt="Logo nicht gefunden" id="logo"></a>

<div id="loginall" class="loginpopup2">

    <form class="popup-content2 animation" action="index.php" method="post">
        <div class="loggedin">
            <div class="loggedin1"><a href="meinkonto.php" id="meinkonto">&nbsp; &nbsp; Mein Konto</a></div>
            <div class="loggedin1"><a href="logout.php" id="abmelden">&nbsp; &nbsp; Abmelden</a></div>
        </div>

    </form>
</div>

<script>
    // Get the modal
    var modal2 = document.getElementById('loginall');

    window.addEventListener("click", function(event) {
        if (event.target === modal2) {
            modal2.style.display = "none";
        }
    });

</script>

</body>
</html>
