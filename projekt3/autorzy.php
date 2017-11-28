<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8" />
	<title>Tytuł aplikacji</title>
	
	<!-- Implementacja Bootstrap oraz stylów -->
	<link rel="stylesheet" href="style/Bootstrap 3.3.7/css/bootstrap.min.css" />
	<link rel="stylesheet" href="style/glowny.css" />
	
	<!-- Metatag odpowiadający za odpowiednie skalowanie na urządzeniach mobilnych -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
	<div class="tresc">
		<?php include "komponenty/naglowek.html" ?>
		
		<div class="margines">
			<h3>Autorzy</h3>
			<p>Pomysłodawcą formatu RFQuiz i wykonawcą części funkcjonalnej aplikacji był dr Robert Fidytek.</p>
			<p>Do powstania wersji finalnej aplikacji przyczynili się członkowie sekcji Aplikacje Sieciowe i Mobilne Koła Naukowego Instytutu Informatyki Stosowanej im. Krzysztofa Brzeskiego w Państwowej Wyższej Szkole Zawodowej w Elblągu:
            <ul>
              <li>Mateusz Cyra - wskazał sposób jak wykryć kodowanie pliku i automatycznie przekonwertować go na format UTF-8,
              <li>Sławomir Chabowski, Adam Gawieńczuk - wykonali interfejs graficzny aplikacji.
            </ul>
            </p>
			
			<?php include "komponenty/stopka.html"; ?>
		</div>
	</div>
</body>
</html>