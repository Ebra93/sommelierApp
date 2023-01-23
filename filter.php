<?php
require_once ('db_connection.php');
global $link;
$sql1 = "select group_concat(distinct art) from getränke Where produkt like 'Wein' group by art";
$sql2 = "select group_concat(distinct art) from getränke Where produkt like 'Bier' group by art";

$result1 = mysqli_query($link, $sql1);
$result2 = mysqli_query($link, $sql2);

function Wein()
{
    global $link, $sql1;
    $result1 = mysqli_query($link, $sql1);
    while($row = mysqli_fetch_row($result1)) {
        echo '<option id="' . $row[0] . '" name="'. $row[0] .'" onclick="BierorWein()">' . $row[0] . '</option>';
    }
}
function Bier() {
    global $link, $sql2;
    $result2 = mysqli_query($link, $sql2);
    while($row = mysqli_fetch_row($result2)) {
        echo '<option id="' . $row[0] . '" name="'. $row[0] .'" onclick="BierorWein()">' . $row[0] . '</option>';
    }
}
?>

<link rel="stylesheet" href="filter.css">
<div class="suchfeld">
    <h1 id="starttext">Jetzt gewähltes Getränk in Deiner Umgebung finden</h1>

    <form action="suche.php" method="get" class="suchleiste">
        <label><input type="search" id="suchanfrage" name="suchanfrage" placeholder="Suchen"></label>
        <input type="submit" value="Suchen" id="suchbutton" class="buttons">
        <input type="button" value="Filter" id="filterbutton" class="buttons" onclick="myFunction()">
        <div id="filter">
            <div id="produkt" name="produkt"></div>
            <div id="art" name="art">
                <div id="artBier">Art:
                    <select id="artBier" name="artBier"><?php Bier(); ?></select></div>
                <div id="artWein">Art:
                    <select id="artWein" name="artWein"><?php Wein(); ?></select></div>
            </div>
            <div id="herkunft" name="herkunft"></div>
            <div id="AlkoholgehaltSlider">
                <div id="alkoholgehalt"></div>
                <div id="slider"></div>
            </div>
            <input type="hidden" id="slidervalueMin" name="slidervalueMin">
            <input type="hidden" id="slidervalueMax" name="slidervalueMax">
            <div id="inhaltsstoffe" name="inhaltsstoffe"></div>
            <div id="bewertung" name="bewertung"></div>
            <div id="allergen" name="allergen"></div>
        </div>
    </form>
    <script>
        document.getElementById("artWein").style.display = "none";
        document.getElementById("artBier").style.display = "none";

        function BierorWein() {
            if(document.getElementById('produkte').value == "Bier") {
                document.getElementById("artWein").style.display = "none";
                document.getElementById("artBier").style.display = "block";
            }
            else if(document.getElementById('produkte').value == "Wein") {
                document.getElementById("artBier").style.display = "none";
                document.getElementById("artWein").style.display = "block";
            }
        }
        function myFunction() {
            document.getElementById("artWein").style.display = "block";
            document.getElementById("artBier").style.display = "none";

            document.getElementById("produkt").innerHTML =
                '<label for="produkt">Produkt:</label>' +
                '<select name="produkte" id="produkte" onclick="BierorWein()"><option value="Wein">Wein</option><option value="Bier">Bier</option></select><br><br>';
            //document.getElementById("art").innerHTML =
            //    '<label for="art">Art</label><br><select name="art"><option value="%">Wein</option><option value="Rotwein">Rotwein</option><option value="Weißwein">Weißwein</option><option value="Rosé">Roséwein</option></select><br>';
            document.getElementById("herkunft").innerHTML =
                '<label for="herkunft">Herkunft</label><br><input type="text" name="herkunft"><br><br>';
            document.getElementById("alkoholgehalt").innerHTML =
                'Alkoholgehalt';

            var slider = document.getElementById('slider');

            noUiSlider.create(slider, {
                range: {
                    'min': 0,
                    'max': 100
                },

                step: 1,

                // Handles start at ...
                start: [0, 100],

                // Display colored bars between handles
                connect: true,

                // Put '0' at the bottom of the slider
                direction: 'ltr',
                orientation: 'horizontal',

                // Move handle on tap, bars are draggable
                behaviour: 'tap-drag',
                tooltips: true,
            });

            var inputNumberMax = document.getElementById('slidervalueMax');
            var inputNumberMin = document.getElementById('slidervalueMin');

            slider.noUiSlider.on('update', function (values, handle) {

                var value = values[handle];

                if (handle) {
                    inputNumberMax.value = value;
                } else {
                    inputNumberMin.value = value;
                }
            });
            inputNumberMax.addEventListener('change', function () {
                slider.noUiSlider.set([null, this.value]);
            });
            inputNumberMin.addEventListener('change', function () {
                slider.noUiSlider.set([this.value, null]);
            });


            document.getElementById("inhaltsstoffe").innerHTML =
                '<label for="inhaltsstoffe">Inhaltsstoffe</label><br><input type="text" name="inhaltsstoffe"><br><br>';
            document.getElementById("bewertung").innerHTML =
                '<label for="bewertung">Bewertung</label>' +
                '<input type="range" id="bewertung" name="bewertung" min="1" max="5" value="3" step="1">' +
                '<div id="bewertungStars">' +
                '<div id="s1" class="filterStars" style="--rating: 5"></div>' +
                '<div id="s2" class="filterStars" style="--rating: 5"></div>' +
                '<div id="s3" class="filterStars" style="--rating: 5"></div>' +
                '<div id="s4" class="filterStars" style="--rating: 5"></div>' +
                '<div id="s5" class="filterStars" style="--rating: 5"></div>' +
                '</div>';
            document.getElementById("allergen").innerHTML =
                '<input type="checkbox" name="allergene"><label for="allergene">Keine Allergene</label>';

        }

        // function produktSelected() {
        //     var selected = document.getElementById('produkte').value;
        //     document.getElementById("art").innerHTML = selected;
        //     if(selected == "Bier") {
        //         document.getElementById("art").innerHTML = '';
        //         document.getElementById("art").innerHTML =
        //             '<label for="art">Art</label><br><select name="art"><option value="%">alles</option><option value="Mischbier">Mischbier</option><option value="Weißwein">Weißwein</option><option value="Rosé">Roséwein</option></select><br>';
        //     } else if (selected == "Wein") {
        //         document.getElementById("art").innerHTML = '';
        //         document.getElementById("art").innerHTML =
        //             '<label for="art">Art</label><br><select name="art"><option value="%">Fisch</option><option value="Rotwein">Rotwein</option><option value="Weißwein">Weißwein</option><option value="Rosé">Roséwein</option></select><br>';
        //     }
        //
        // }
    </script>