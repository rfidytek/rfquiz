<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8" />
	<title>Tytuł aplikacji</title>
	
	<!-- Implementacja Bootstrap oraz stylów -->
	<link rel="stylesheet" href="style/Bootstrap 3.3.7/css/bootstrap.min.css" />
	<link rel="stylesheet" href="style/glowny.css" />
	<link rel="stylesheet" href="style/konwerter.css" />
	
	<!-- Metatag odpowiadający za odpowiednie skalowanie na urządzeniach mobilnych -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
	<div class="tresc-alt">
	<?php include "komponenty/naglowek.html"; ?>
	<!--<span style="display: none;">-->
		<?php require_once "komponenty/konwerter.php"; ?>
	<!--</span>-->
		<span class="tresc-konwerter">
		
		
			<div class="margines">
				<div class="pole-zakladki">
						<ul class="zakladki grupa">
							<li><a href="#wpisz">Pole tekstowe</a></li>
							<li><a href="#upload">Załaduj plik</a></li>
						</ul>
						
						
					<div class="wnetrze">
					
						<div id="wpisz">
							<form method="post" action="#">
								<textarea id="tresc" name="tresc" placeholder="Tutaj wpisz treść zadań. Następnie kliknij odpowiedni przycisk, by przekonwertować je do odpowiedniego formatu."></textarea>
								<p>W jakim formacie chcesz pobrać test?</p>
								<button type="submit" name="pobierz" value="pobierz1">MoodleXML</button>
								<button type="submit" name="pobierz" value="pobierz2">HTML</button>
							</form>
						</div>
						
						<div id="upload">
							<br />
							<form action="konwerter.php#upload" method="POST" ENCTYPE="multipart/form-data">
								<p>Wybierz ze swojego urządzenia plik w formacie ".txt", po czym kliknij odpowiedni przycisk.</p>
								<br />
							    <input type="file" name="plik" />
								<input type="submit" name="zaladuj" value="Załaduj plik" id="zaladuj" />
								<br /><br />
								<p>W jakim formacie chcesz pobrać plik?</p>
								<button type="submit" name="pobierz" value="pobierz3">MoodleXML</button>
								<button type="submit" name="pobierz" value="pobierz4">HTML</button>
							</form>
							<br />
						</div>	
						
					</div>
					
					
				</div>
				
				
				<?php include "komponenty/stopka.html"; ?>
			</div>
		</span>		
	</div>
</body>