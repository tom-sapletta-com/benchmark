# Benchmark Linux - uniwersalny skrypt testujący CPU, RAM, dysk i GPU


Ten projekt to prosty, uniwersalny skrypt bash do przeprowadzenia podstawowych testów wydajnościowych na systemach Linux: Fedora, Ubuntu, Debian, Arch Linux i innych.

Skrypt automatycznie wykrywa używany system i menadżera pakietów, instaluje potrzebne narzędzia (`sysbench`, `glmark2`), zbiera szczegółowe informacje o sprzęcie i systemie oraz wykonuje benchmarki:

- CPU (obliczanie liczb pierwszych)
- RAM (transfers pamięci)
- Dysk (zapis 1 GB)
- GPU (test OpenGL glmark2 w skróconej wersji, by benchmark zmieścił się poniżej 5 minut)

Wyniki zapisywane są do pliku CSV, co ułatwia ich archiwizację i porównywanie.



## Zawartość projektu

- `benchmark-linux.sh` - główny skrypt bash do uruchomienia testów  
- `README.md` - niniejsza dokumentacja

---

## Wymagania

- System Linux (Fedora, Ubuntu, Debian, Arch Linux, CentOS lub inne)  
- Dostęp do konta z uprawnieniami `sudo` do instalacji pakietów i wykonywania testów  
- Połączenie z internetem do pobrania potrzebnych pakietów (opcjonalnie, jeśli już ich nie masz)

---

## Sposób użycia

1. Pobierz lub skopiuj plik `benchmark-linux.sh` na testowaną maszynę.  
2. Nadaj mu prawa wykonania:

   ```
   chmod +x benchmark-linux.sh
   ```

3. Uruchom skrypt:

   ```
   ./benchmark-linux.sh
   ```

4. Skrypt wyświetli informacje o systemie i sprzęcie, zainstaluje pakiety (jeśli brak) i przeprowadzi testy.  
5. Wyniki testów zostaną zapisane do pliku o nazwie w stylu `benchmark_results_YYYYMMDD_HHMMSS.csv` w bieżącym katalogu.  
6. Log szczegółowy GPU znajduje się w pliku `glmark2.log`.

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
