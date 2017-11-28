<!doctype html>
<html lang="pl">
<head>
	<meta charset="UTF-8" />
	<title>Tytuł aplikacji</title>
	
	<!-- Implementacja Bootstrap oraz stylów -->
	<link rel="stylesheet" href="style/Bootstrap 3.3.7/css/bootstrap.min.css" />
	<link rel="stylesheet" href="style/glowny.css" />
	<link rel="stylesheet" href="style/pytania.css" />
	
	<!-- Metatag odpowiadający za odpowiednie skalowanie na urządzeniach mobilnych -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
	<div class="tresc-alt">
		
		<?php include "komponenty/naglowek.html" ?>
		
		<div class="margines">
			<h2>Instrukcja tworzenia pytań testowych w formacie RFQuiz</h2>
			<p>Pytania można wprowadzać w dwojaki sposób: poprzez wybranie pliku z dysku (zapisanego w formacie
			*.txt, czyli utworzonego np. w notatniku) lub poprzez pisanie pytań w odpowiednim polu
			edytora online (sposób niezalecany - w przypadku utracenia połączenia z siecią Internet można utracić wprowadzone pytania).
			Aplikacja konwertuje pytania zapisane w formacje RFQuiz na format MoodleXML, który umożliwia zaimportowanie pytań testowych do platformy Moodle.
			
			
			</p>
			
			<p>Pisanie pytań należy zacząć od napisania nazwy kategorii i oddzielić ją pustą linią.
			Następne należy wpisywać pytania, które też należy oddzielać pustymi liniami.
			</p>
			
			
			<h3>1. Pytania jednokrotnego i wielokrotnego wyboru</h3>
			<p>Treść pytania jednokrotnego lub wielokrotnego wyboru może składać się z jednej lub wielu linii tekstu.
			Każda poprawna odpowiedź musi być poprzedzona znakami "<b>1|</b>", a niepoprawna odpowiedź musi być poprzedzona znakami "<b>0|</b>".
			Każde pytanie jednokrotnego lub wielokrotnego wyboru musi mieć co najmniej dwie odpowiedzi. Każda odpowiedź powinna się zawierać w jednej linii tekstu.<br>
			Jeżeli pierwsza wprowadzona odpowiedź jest poprawna, a wszystkie pozostałe błędne, to pytanie zostanie potraktowane jako pytanie jednokrotnego wyboru. W pozostałych przypadkach pytanie zostanie potraktowane jako pytanie wielokrotnego wyboru.
			</p>



			<p class="tytul-pytania">Przykładowe pytanie jednokrotnego wyboru:</p>
			<p class="pytanie">
            Wskaż najdłuższą rzekę w Polsce.<br>
            1|Wisła<br>
            0|Odra<br>
            0|Dunaj<br>
            0|Warta			
			</p>

			<p class="tytul-pytania">Przykładowe pytanie wielokrotnego wyboru:</p>
			<p class="pytanie">
			Wskaż polskie rzeki.<br>
			1|Wisła<br>
			0|Nil<br>
			1|Odra<br>
			0|Tamiza
			</p>
			
			<p class="tytul-pytania">Przykładowe pytanie wielokrotnego wyboru z jedną poprawną odpowiedzią:</p>
			<p class="pytanie">
			Wskaż najdłuższą rzekę w Polsce.<br>
			0|Warta<br>
			1|Wisła<br>
			0|Odra<br>
			0|Dunaj
			</p>
					
			
			<h3>2. Pytania typu prawda/fałsz</h3>
			<p>Pytanie typu prawda/fałsz powinno składać się z jednej linii tekstu.
			Odpowiedź prawdziwa powinna się rozpoczynać od znaków "<b>1|</b>", a odpowiedź fałszywa powinna się rozpoczynać od znaków "<b>0|</b>".
            </p> 
			
			<p class="tytul-pytania">Przykładowe pytanie typu prawda/fałsz:</p>
			<p class="pytanie">1|Najdłuższa polska rzeka to Wisła.</p>
			
			
			<h3>3. Pytania typu krótka odpowiedź</h3>
			<p>Są to pytania, na które należy odpowiedzieć poprzez napisanie krótkiego tekstu. Muszą posiadać co najmniej jedną odpowiedź poprawną i mogą posiadać dowolną ilość odpowiedzi częściowo poprawnych.
			Odpowiedzi prawidłowe powinny zostać poprzedzone znakami "<b>100%|</b>", natomiast jeśli odpowiedź jest częściowo poprawna np. w 50%, to powinna zostać poprzedzona znakami "<b>50%|</b>". Każda odpowiedź powinna zostać umieszczona w osobnej linii.
			</p>
			
			<p class="tytul-pytania">Przykładowe pytanie typu krótka odpowiedź:</p>
			<p class="pytanie">
			Jakie miasto jest stolicą Polski?<br>
			100%|Warszawa<br>
			80%|Wwa<br>
			80%|W-wa<br>
			10%|W
			</p>
			
			
			<h3>4. Pytania typu dopasuj odpowiedzi</h3>
			<p>Pytania posiadają tyle samo pytań, co odpowiedzi. Do każdego pytania należy dopasować jedną odpowiedź. Pytania i odpowiedzi nie powinny się powtarzać.<br>Treści pytań i odpowiedzi powiny być poprzedzone polecenie wprowadzającym informującym, co należy zrobić. Każde pytanie i odpowiedź powinny być wprowadzone w osobnej linii w formacie "<b>treść pytania|treść odpowiedzi</b>". Treści odpowiedzi powinny być krótkie.
            </p>
			
			<p class="tytul-pytania">Przykładowe pytanie typu dopasuj odpowiedzi:</p>
			<p class="pytanie">
			Dopasuj liczebniki główne.<br>
			thirteen|13<br>
			fourteen|14<br>
			fifteen|15<br>
			sixteen|16<br>
			seventeen|17<br>
			eighteen|18<br>
			nineteen|19
			</p>
			
			<h3>5. Pytania zagnieżdzone (uzupełnianka w tekście)</h3>
			<p>Pytania zagnieżdzone umożliwiają umieszczenie w tekście pytań z krótką odpowiedzią lub listy rozwijanej z jedną poprawną odpowiedzią.
			Pytania z krótką odpowiedzią powinny zostać umieszczone w tekście w formacie "<b> |odpowiedź| </b>".
			Listy rozwijane z jedną poprawną odpowiedzią powinny być umieszczone w tekście w formacie "<b> |odp. poprawna|odp. niepoprawna 1|odp. niepoprawna 2|odp. niepoprawna n| </b>".
			W obu przypadkach treść odpowiedzi powinna się rozpoczynać znakami " |" (spacja i znak "|"), a kończyć znakami "| " (znak "|" i spacja). Znaki "|" znajdujące się wewnątrz listy rozwijaniej nie mogą być poprzedzone i zakończone znakiem spacji.
            </p>
			
			<p class="tytul-pytania">Przykładowe pytanie zagnieżdżone:</p>
			<p class="pytanie">
			Książkę pt. "Krzyżacy" napisał |Henryk Sienkiewicz|Adam Mickiewicz|Juliusz Słowacki|Jan Długosz|Jan Matejko|. 
			Jest to powieść |historyczna|przygodowa|fantasy|podróżnicza|epistolarna|. 
			Główny bohater to |Zbyszko| z Bogdańca herbu |Tępa Podkowa|, bratanek Maćka z Bogdańca.
			</p>
			
			
			
			
			<?php include "komponenty/stopka.html"; ?>
			
		</div>
	</div>
</body>
</html>
