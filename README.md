# Benchmark Linux - uniwersalny skrypt testujący CPU, RAM, dysk, GPU i AI

Ten projekt to uniwersalny skrypt bash do przeprowadzenia kompleksowych testów wydajnościowych na systemach Linux: Fedora, Ubuntu, Debian, Arch Linux i innych.

Skrypt automatycznie wykrywa używany system i menadżera pakietów, instaluje potrzebne narzędzia (`sysbench`, `glmark2`, `python3`, `numpy`, `scikit-learn`), zbiera szczegółowe informacje o sprzęcie i systemie oraz wykonuje benchmarki:

- CPU (obliczanie liczb pierwszych)
- RAM (transfers pamięci)
- Dysk (zapis 1 GB)
- GPU (test OpenGL glmark2 w skróconej wersji, by benchmark zmieścił się poniżej 5 minut)
- AI (operacje NumPy i uczenie maszynowe z scikit-learn)

Wyniki zapisywane są do pliku CSV, co ułatwia ich archiwizację i porównywanie. Dodatkowo, narzędzie oferuje wizualizację wyników w formie wykresu radarowego oraz możliwość porównania wyników z różnych systemów.



## Zawartość projektu

- `benchmark.sh` - główny skrypt bash do uruchomienia testów
- `ai_benchmark.py` - skrypt Python do testów wydajności AI
- `install.sh` - skrypt instalacyjny (one-liner)
- `index.html` - wizualizacja wyników w formie wykresu radarowego z lokalnym przechowywaniem historii
- `index.php` - aplikacja serwerowa do porównywania wyników
- `publish.sh` - skrypt do publikowania wyników na serwerze
- `Makefile` - automatyzacja zadań projektowych
- `Dockerfile` i `docker-compose.yml` - konfiguracja środowiska Docker
- `.env` - plik konfiguracyjny dla skryptów i Dockera
- `CHANGELOG.md` - historia zmian w projekcie
- `CONTRIBUTING.md` - wytyczne dla współtwórców
- `README.md` - niniejsza dokumentacja

---

## Wymagania

- System Linux (Fedora, Ubuntu, Debian, Arch Linux, CentOS lub inne)  
- Dostęp do konta z uprawnieniami `sudo` do instalacji pakietów i wykonywania testów  
- Połączenie z internetem do pobrania potrzebnych pakietów (opcjonalnie, jeśli już ich nie masz)
- Python 3.6+ i pip (dla testów AI)
- Docker i Docker Compose (opcjonalnie, dla środowiska kontenerowego)

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

### Standardowe uruchomienie

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

### Używanie Makefile

Projekt zawiera Makefile do automatyzacji typowych zadań:

```bash
# Uruchomienie benchmarku
make benchmark

# Instalacja zależności
make install-deps

# Publikowanie wyników
make publish

# Czyszczenie plików tymczasowych
make clean

# Wyświetlenie dostępnych poleceń
make help
```

### Używanie Dockera

Możesz uruchomić benchmark w środowisku Docker:

```bash
# Zbudowanie obrazu Docker
make docker-build

# Uruchomienie kontenera
make docker-run

# Zatrzymanie kontenera
make docker-stop

# Usunięcie kontenerów i obrazów
make docker-clean
```

Po uruchomieniu kontenera, interfejs webowy będzie dostępny pod adresem http://benchmark.local lub http://localhost.

---

## Funkcje

### Testy wydajnościowe

- **CPU**: Test obliczania liczb pierwszych z sysbench
- **RAM**: Test transferu pamięci z sysbench
- **Dysk**: Test szybkości zapisu z dd
- **GPU**: Test OpenGL z glmark2
- **AI**: Test operacji NumPy (mnożenie macierzy, inwersja, SVD) i uczenia maszynowego (RandomForest)

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

### Konfiguracja

Projekt używa pliku `.env` do konfiguracji parametrów benchmarku, Dockera i serwera. Możesz dostosować:
- Parametry testów (CPU_MAX_PRIME, CPU_THREADS, DISK_TEST_SIZE, itp.)
- Konfigurację Dockera (DOCKER_DOMAIN, DOCKER_PORT)
- Ustawienia serwera (SERVER_URL, UPLOAD_DIR)

---

## Format wyników

Plik CSV zawiera kolumny:

| Kolumna    | Opis                                  |
|------------|-------------------------------------|
| test       | Nazwa testu (np. CPU_total_time)    |
| wartosc    | Wynik testu                         |
| jednostka  | Jednostka wyniku (np. `s`, `MB/s`, `pkt`) |
| system     | Pełna nazwa dystrybucji Linux       |
| cpu_model  | Model procesora                     |
| kernel     | Wersja jądra Linux                  |
| gpu        | Informacje o karcie graficznej       |
| ram_total  | Pojemność RAM                      |
| disks      | Lista dysków i ich rozmiarów         |
| data       | Data i czas wykonania testu          |

Główne metryki wyników:

| Test       | Metryka                             | Jednostka | Interpretacja |
|------------|-------------------------------------|-----------|---------------|
| CPU        | CPU_total_time                      | s         | Mniej = lepiej |
| RAM        | RAM_transfer_rate                   | MiB/s     | Więcej = lepiej |
| Dysk       | DISK_write_speed                    | MB/s      | Więcej = lepiej |
| GPU        | GPU_score                           | pkt       | Więcej = lepiej |
| AI         | AI_total_score                      | pkt       | Więcej = lepiej |

---

## Modyfikacje i dostosowanie

- Możesz zmienić parametry testów w pliku `.env` lub bezpośrednio w skrypcie.
- Skrypt łatwo rozszerzyć o dodatkowe testy lub monitorowanie parametrów sprzętowych (np. temperatury).
- Możesz dostosować testy AI w pliku `ai_benchmark.py` do swoich potrzeb.
- Plik CSV można użyć do automatycznych porównań wyników lub wizualizacji danych.
- Środowisko Docker można dostosować modyfikując `Dockerfile` i `docker-compose.yml`.

## Rozwój projektu

Informacje dla deweloperów i współtwórców:

- Zapoznaj się z plikiem `CONTRIBUTING.md`, aby dowiedzieć się jak wnieść swój wkład do projektu.
- Historia zmian jest dokumentowana w pliku `CHANGELOG.md`.
- Użyj `Makefile` do automatyzacji typowych zadań deweloperskich.

---

## Licencja

Projekt udostępniony na licencji Apache — możesz go dowolnie wykorzystywać i modyfikować.

---

## Kontakt

Masz pytania, sugestie lub potrzebujesz pomocy?  
Napisz do mnie lub zgłoś problem na stronie projektu.
