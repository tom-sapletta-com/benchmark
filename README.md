# Benchmark Linux - uniwersalny skrypt testujący CPU, RAM, dysk i GPU

Ten projekt to prosty, uniwersalny skrypt bash do przeprowadzenia podstawowych testów wydajnościowych na systemach Linux: Fedora, Ubuntu, Debian, Arch Linux i innych.

Skrypt automatycznie wykrywa używany system i menadżera pakietów, instaluje potrzebne narzędzia (`sysbench`, `glmark2`), zbiera szczegółowe informacje o sprzęcie i systemie oraz wykonuje benchmarki:

- CPU (obliczanie liczb pierwszych)
- RAM (transfers pamięci)
- Dysk (zapis 1 GB)
- GPU (test OpenGL glmark2 w skróconej wersji, by benchmark zmieścił się poniżej 5 minut)

Wyniki zapisywane są do pliku CSV, co ułatwia ich archiwizację i porównywanie. Dodatkowo, narzędzie oferuje wizualizację wyników w formie wykresu radarowego oraz możliwość porównania wyników z różnych systemów.



## Zawartość projektu

- `benchmark.sh` - główny skrypt bash do uruchomienia testów
- `install.sh` - skrypt instalacyjny (one-liner)
- `index.html` - wizualizacja wyników w formie wykresu radarowego z lokalnym przechowywaniem historii
- `publish.sh` - skrypt do publikowania wyników na serwerze
- `README.md` - niniejsza dokumentacja

---

## Wymagania

- System Linux (Fedora, Ubuntu, Debian, Arch Linux, CentOS lub inne)  
- Dostęp do konta z uprawnieniami `sudo` do instalacji pakietów i wykonywania testów  
- Połączenie z internetem do pobrania potrzebnych pakietów (opcjonalnie, jeśli już ich nie masz)

---

## Szybka instalacja (one-liner)

Możesz zainstalować i uruchomić benchmark za pomocą jednej komendy:

```bash
curl -s https://raw.githubusercontent.com/tom-sapletta-com/benchmark/main/install.sh | bash
```

Lub pobierz i uruchom ręcznie:

```bash
wget https://raw.githubusercontent.com/tom-sapletta-com/benchmark/main/install.sh
chmod +x install.sh
./install.sh
```

## Sposób użycia

1. Pobierz repozytorium lub użyj skryptu instalacyjnego jak opisano powyżej.
2. Uruchom skrypt benchmark:

   ```bash
   ./benchmark.sh
   ```

3. Skrypt wyświetli informacje o systemie i sprzęcie, zainstaluje pakiety (jeśli brak) i przeprowadzi testy.
4. Wyniki testów zostaną zapisane do pliku o nazwie w stylu `benchmark_results_YYYYMMDD_HHMMSS.csv` w bieżącym katalogu.
5. Aby zobaczyć wizualizację wyników, otwórz plik `index.html` w przeglądarce.
6. Aby opublikować wyniki na serwerze, użyj skryptu `publish.sh`:

   ```bash
   ./publish.sh
   ```

---

## Nowe funkcje

### Wizualizacja wyników

Plik `index.html` oferuje:
- Wykres radarowy do porównania wyników
- Lokalne przechowywanie historii benchmarków (PWA Storage API)
- Tabelę porównawczą wyników
- Informacje o testowanych urządzeniach

### Publikowanie wyników

Możesz publikować swoje wyniki na serwerze benchmark.sapletta.com za pomocą skryptu `publish.sh`. Skrypt automatycznie wysyła ostatni wygenerowany plik CSV na serwer, gdzie możesz porównać swoje wyniki z innymi użytkownikami.

### Porównywanie wyników online

Strona benchmark.sapletta.com umożliwia porównanie wyników z różnych systemów. Możesz:
- Przeglądać wyniki innych użytkowników
- Porównywać różne konfiguracje sprzętowe
- Analizować trendy wydajnościowe

---

## Format wyników

Plik CSV zawiera kolumny:

| Kolumna    | Opis                                  |
|------------|-------------------------------------|
| test       | Nazwa testu (np. CPU_total_time)    |
| wartosc    | Wynik testu                         |
| jednostka  | Jednostka wyniku (np. `s`, `MB/s`) |
| system     | Pełna nazwa dystrybucji Linux       |
| cpu_model  | Model procesora                     |
| kernel     | Wersja jądra Linux                  |
| gpu        | Informacje o karcie graficznej       |
| ram_total  | Pojemność RAM                      |
| disks      | Lista dysków i ich rozmiarów         |
| data       | Data i czas wykonania testu          |

---

## Modyfikacje i dostosowanie

- Możesz zmienić w skrypcie zestaw scen testu GPU lub rozdzielczość w poleceniu `glmark2`, aby dostosować czas testu.  
- Skrypt łatwo rozszerzyć o dodatkowe testy lub monitorowanie parametrów sprzętowych (np. temperatury).  
- Plik CSV można użyć do automatycznych porównań wyników lub wizualizacji danych.

---

## Licencja

Projekt udostępniony na licencji Apache — możesz go dowolnie wykorzystywać i modyfikować.

---

## Kontakt

Masz pytania, sugestie lub potrzebujesz pomocy?  
Napisz do mnie lub zgłoś problem na stronie projektu.
