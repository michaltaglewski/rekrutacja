## Pierwsze kroki i decyzje archtektoniczne

1. Pierszą rzeczą, którą zauważyłem w strukturze aplikacji jest katalog "Likes", który "sugeruje" strukturę domenową,
   ale wewnątrz już widać, że jest to pomieszanie wartwy domenowej i infrastrukturalnej. Zdecydowałem, by pójść drogą DDD i
   "wyprostować" strukturę, wprowadzając różne warstwy aplikacji.
2. Zanim jednak zacząłem modyfikacje, postanowiłem doinstalować narzędzie do analizy statycznej kodu code sniffer
   (nie było to bardzo konieczne, ale lubię trzymać porządek od początku).
4. Logikę pobierania użytkownika po tokenie i username w AuthController przeniosłem do App\User\Application\Service\UserService.
5. Generalnie w controllerach widzę, potrzebę wydzielenia logiki biznesowej do innych miejsc np. Services lub Commands. 
Widzę też możliwość zmniejszenia zależności metod (requestów) np. od EntityManagera, 
pozostawiając minimum parametrów (idealnie tylko obiekt Request). Cała zależność DI controllera może ograniczać się do 
tworów domenowych takich jak repozytorium czy serwis.
6. Wraz ze zmianami j/w dopisałem kilka podstawowych testów unitowych serwisów do tego aby potwierdzić sobie,
   że po dalszych modfikacjach, nie naruszę logiki aplikacji.
7. Dodałem brakującą warstwę infrastruktury dla AuthToken i User, tworząc odpowiednie repozytoria.
8. Po przeanalizowaniu controllera PhotoController oraz domeny Like, stwierdziłem, 
że nie podoba mi logika w której to Like jest tą dominującą encją nad Photo - przykładowo 
lajkowanie zdjęcia poprzez repozytorium lajków. Już nawet samo słowo "Like" wskazuje, że jest czynność 
a więc pasuje idealnie jako metoda encji Photo. Jest to odwrócenie root aggregate. 
W związku z tym pozbyłem się części metod z LikeRepository i przeniosłem do encji Photo. Repozytorium Photo nie wymaga już dalej
osobnych metod do lajkowania i unlajkowania.


## Naprawione błędy i różne poprawki
- w composer.json autoload psr-4 wyglądał jakby miał błąd w zapisie namespace "App\\\\", zmieniłem na "App\\"
- AuthController miał błąd polegający na tym, że token autoryzacyjny i użytkownik był sprawdzany niezależnie, 
pomijając sprawdzanie czy istniejący token należy do danego użytkownika