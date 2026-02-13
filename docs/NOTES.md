## Pierwsze kroki i decyzje archtektoniczne

1. Pierwszą rzeczą, którą zauważyłem w strukturze aplikacji jest katalog "Likes", który "sugeruje" strukturę domenową,
   ale wewnątrz już widać, że jest to pomieszanie wartwy domenowej i infrastrukturalnej. Zdecydowałem, by pójść drogą DDD i
   "wyprostować" strukturę, wprowadzając różne warstwy aplikacji. Chociaż finalnie okazało się, że jest to takie DDD, 
   które wymagało pewnych kompromisów :)
2. Zanim jednak zacząłem modyfikacje, postanowiłem doinstalować narzędzie do analizy statycznej kodu code sniffer
   (nie było to bardzo konieczne, ale lubię trzymać porządek od początku).
4. Logikę pobierania użytkownika po tokenie i username w AuthController przeniosłem do App\User\Application\Service\UserService.
5. Generalnie w controllerach, widzę potrzebę wydzielenia logiki biznesowej do innych miejsc np. Services lub Commands. 
   Widzę też możliwość zmniejszenia zależności metod (requestów) np. od EntityManagera, 
   pozostawiając minimum parametrów (idealnie tylko obiekt Request). Cała zależność DI controllera może ograniczać się do 
   tworów domenowych takich jak repozytorium czy serwis.
6. Wraz ze zmianami j/w dopisałem kilka podstawowych testów unitowych serwisów do tego aby potwierdzić sobie,
   że po dalszych modfikacjach, nie naruszę logiki aplikacji.
7. Dodałem brakującą warstwę infrastruktury dla AuthToken i User, tworząc odpowiednie repozytoria.
8. Po przeanalizowaniu controllera PhotoController oraz domeny Like, stwierdziłem, 
   że nie podoba mi się logika w której to Like jest tą dominującą encją nad Photo - przykładowo 
   lajkowanie zdjęcia poprzez repozytorium lajków. Już nawet samo słowo "Like" wskazuje, że jest czynność 
   a więc pasuje idealnie jako metoda encji Photo. Jest to odwrócenie root aggregate. 
   W związku z tym pozbyłem się części metod z LikeRepository i przeniosłem do encji Photo. Repozytorium Photo nie wymaga już dalej
   osobnych metod do lajkowania i unlajkowania.
9. Uprościłem HomeController i pozbyłem się zależności od repozytoriów doctrine, zastępując interfejsami. 
   Dostosowałem strukturę katalogu i przeniosłem do /src/Ui/Http/Controller/HomeController.php
10. Encje doctrinowe miały bezpośrednie relacje z innymi encjami (np Like z User), co samo w sobie zupełnie nie jest błędem, 
   ale postanowiłem zastąpić je identyfikatorami. Ma to znaczenie w miejscach, gdzie encje różnych domen "komunikują" się ze sobą, 
   z racji że w DDD nie wolno mieszać obiektów pomiędzy bounded contextami, takie podejście ułatwi dalszy rozwój aplikacji.

Podsumowując być może niektóre zmiany architektoniczne były tutaj overkill i mając więcej czasu być może rozplanowałbym 
to inaczej/lepiej, ale wydaje mi się, że w ten spsoób wyraźniej zaznaczyłem mój tok myślenie o DDD i wyraźnym 
oddzieleniu domeny od infrastruktury. Dodam także, że nie jestem przekonany również o wyborze identyfikatorów incremenatywnych 
w encjach. Jest to nie tylko pewnie zgrzyt z DDD, ale także i kwestia bezpieczeństwa danych, kiedy takimi identyfikatorami operuje się
jawnie po stronie clienta (przeglądarki).

## Zadanie 1
Utworzyłem nową tabelę bazy danych `phoenix_access_tokens`, która przechowuje tokeny autoryzacyjne do API Phoenix. 
Można zapisywać w profilu użytkownika.

## Zadanie 2
Dodałem klienta API Phoenix, który następnie w ProfileControllerze pozwala na import zdjęć. Do poprawnej autoryzacji 
potrzebne jest ustawienie nagłówka z access tokenem (z zadania nr 1). W przypadku błędnego tokenu użytkownik dostaje komunikat o
błędzie autoryzacji. Tutaj komunikacja z API Phoenix wymaga jeszcze dopracowania. Brakuje dobrej walidacji 
czy nawet error handlingu po stronie clienta. 
Niezbędne będzie dodanie też walidacji czy użytkownik użył access tokenu, który należy do niego oraz 
czy jest autorem zdjęć, które pobiera.

## Zadanie 3
Niestety ze względu na kończący się czas, dodałem jedynie bardzo prymitywne filtrowanie po parametrze GET w adresie URL, mp:
`http://localhost:8000/?location=Alaska` czy http://localhost:8000/?description=view%20of%20snow-capped. 
Bez zmian w UI użytkownika.

## Naprawione błędy i różne poprawki
- w composer.json autoload psr-4 wyglądał jakby miał błąd w zapisie namespace "App\\\\", zmieniłem na "App\\"
- AuthController miał błąd polegający na tym, że token autoryzacyjny i użytkownik był sprawdzany niezależnie, 
pomijając sprawdzanie czy istniejący token należy do danego użytkownika
- naprawiłem zapytania SQL w akcji podczas logowania użytkownika, które miały podatność na SQL injection
- w HomeController phpdoc błędnie wskazywał na typ zwracany JsonResponse. Poprawiłem + zmieniłem nową wersję atrybutów PHP
- generalnie wolę unikać nazw zmiennych typu `$em` (entity manager), oczywiście skróty same w sobie nie są złe oraz 
gdy taka jest akurat przyjęta konwencja czy zasada w zespole i wszyscy rozumieją "co to jest". 
Dla mnie `$entityManager` czyta się o wiele lepiej (KISS).

## Co dalej?
Nie udało mi się wprowadzić wielu zmian, które można było wprowadzić. Chociażby brak walidacji requestów controlerach. 
Również wartoby już od samego początku w większym stopniu pokryć testami.
Z rzeczy bardziej architektonicznych, można by pewnie lepiej przemyśleć domeny czy wprowdadzić 
jakieś obiekty pomocnicze np. ValueObject czy lepiej napisać Mappery. 
Rózne przemyślenia i uwagi zostawiłem bezpośrednio w kodzie z adnotacją `@TODO`

## Jak wykorzystałem AI?
Podczas wykonywania zadania wspierałem się również AI, chociaż raczej w formie completions w celu szybszego tworzenia 
kodu czy szybszego pisania wiadomości do commitów. Agentem AI wspomogłem się nieco przy dokonywaniu zmian w konfiguracji symfony czy 
modyfikacjach w encjach i repozytoriach.
