<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8" />
	<title>Konwenter RFQuiz</title>
	
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
			<p>Aplikacja służy do konwersji pytań testowych zapisanych w formacie RFQuiz na format MoodleXML.
			Powstanie formatu <a href="instrukcja.php">RFQuiz</a> przyśpieszyło i uprościło proces tworzenia pytań testowych. 
			Jest on na tyle intuicyjny, że nawet uczniowie szkoły podstawowej są w stanie w nim samodzielnie tworzyć pytania testowe nawet w przypadku braku dostępu do platformy Moodle.
			</p>
		</div>
		
		<div class="zielony">
			<div class="zielony-link">
				<a href="konwerter.php" class="link-do-edytora">Kliknij by przejść do edytora&nbsp;<span class="glyphicon glyphicon-arrow-right"></span></a>
			</div>
			<p class="zmniejsz">Możesz wpisać treść testu bezpośrednio na stronie, lub też załadować gotowy plik z treścią testu.
			Po przekonwertowaniu treści, plik z gotowym testem można pobrać i załadować bezpośrednio na platformę Moodle.</p>
					
			
			<?php include "komponenty/stopka.html"; ?>
		</div>
	</div>
</body>
</html>