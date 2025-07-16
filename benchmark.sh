#!/bin/bash

set -e

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
RESULTS_FILE="benchmark_results_${TIMESTAMP}.csv"

echo "=== Informacje o systemie i sprzęcie ==="

DATE_NOW=$(date)
HOSTNAME=$(hostname)
OS_NAME="Unknown"
if [ -f /etc/os-release ]; then
  . /etc/os-release
  OS_NAME=$PRETTY_NAME
fi
KERNEL=$(uname -r)
CPU_MODEL=$(lscpu | grep 'Model name' | cut -d ':' -f2 | xargs || echo "unknown")
GPU_INFO=$({ lspci | grep -i 'vga\|3d\|2d'; } 2>/dev/null || echo "unknown")
RAM_TOTAL=$(free -h | grep Mem: | awk '{print $2}' || echo "unknown")
DISKS=$(lsblk -d -o NAME,SIZE,TYPE | grep disk | awk '{print $1":"$2}' | paste -sd "," -)

echo "Data i godzina: $DATE_NOW"
echo "Hostname: $HOSTNAME"
echo "System: $OS_NAME"
echo "Kernel: $KERNEL"
echo "CPU: $CPU_MODEL"
echo "GPU: $GPU_INFO"
echo "RAM: $RAM_TOTAL"
echo "Dyski: $DISKS"

echo
echo "--- Rozpoczynam benchmark ---"

install_packages() {
  PACKAGES="$1"
  if command -v dnf &>/dev/null; then
    echo "System wykryto jako Fedora/RHEL"
    sudo dnf install -y $PACKAGES
  elif command -v apt-get &>/dev/null; then
    echo "System wykryto jako Debian/Ubuntu"
    sudo apt-get update
    sudo apt-get install -y $PACKAGES
  elif command -v yum &>/dev/null; then
    echo "System wykryto jako starszy RHEL/CentOS"
    sudo yum install -y $PACKAGES
  elif command -v pacman &>/dev/null; then
    echo "System wykryto jako Arch Linux"
    sudo pacman -Sy --noconfirm $PACKAGES
  else
    echo "Nie wykryto menedżera pakietów. Proszę zainstalować ręcznie: $PACKAGES"
    exit 1
  fi
}

install_packages "sysbench glmark2"

if ! command -v sysbench &>/dev/null || ! command -v glmark2 &>/dev/null; then
  echo "Brak wymaganych narzędzi sysbench lub glmark2. Przerywam test."
  exit 1
fi

echo "Przygotowuję plik wyników: $RESULTS_FILE"
echo "test,wartosc,jednostka,system,cpu_model,kernel,gpu,ram_total,disks,data" > "$RESULTS_FILE"

save_result() {
  echo "$1,$2,$3,\"$OS_NAME\",\"$CPU_MODEL\",\"$KERNEL\",\"$GPU_INFO\",\"$RAM_TOTAL\",\"$DISKS\",\"$DATE_NOW\"" >> "$RESULTS_FILE"
}

echo "=== Benchmark CPU ==="
cpu_output=$(sysbench cpu --cpu-max-prime=20000 --threads=4 run)
echo "$cpu_output"
cpu_time=$(echo "$cpu_output" | grep "total time:" | awk '{print $3}')
save_result "CPU_total_time" "$cpu_time" "s"

echo -e "\n=== Benchmark RAM ==="
ram_output=$(sysbench memory run)
echo "$ram_output"
ram_speed=$(echo "$ram_output" | grep transferred | sed -n 's/.*(\([0-9.]\+\) \([A-Za-z\/]\+\)).*/\1/p')
ram_unit=$(echo "$ram_output" | grep transferred | sed -n 's/.*(\([0-9.]\+\) \([A-Za-z\/]\+\)).*/\2/p')
save_result "RAM_transfer_rate" "$ram_speed" "$ram_unit"

echo -e "\n=== Benchmark dysku (zapis 1 GB) ==="
dd_output=$(dd if=/dev/zero of=./testfile bs=1G count=1 oflag=dsync 2>&1)
echo "$dd_output"
disk_speed=$(echo "$dd_output" | grep -o '[0-9.]* [MG]B/s' | tail -n1 | awk '{print $1}')
disk_unit=$(echo "$dd_output" | grep -o '[0-9.]* [MG]B/s' | tail -n1 | awk '{print $2}')
save_result "Disk_write_speed" "$disk_speed" "$disk_unit"

echo -e "\n=== Benchmark GPU (glmark2, skrócony test) ==="
echo "Uruchamiam glmark2 z limitowanym czasem 4 minuty i wybranymi testami..."
glmark2_output=$(timeout 240 glmark2 --size 400x300 -s build,texture,shading,bump 2>&1 | tee glmark2.log)

# próbujemy wydobyć wynik z różnych formatów wyjścia
glmark2_score=$(echo "$glmark2_output" | grep -E "Score:" | grep -oE '[0-9]+(\.[0-9]+)?' | tail -1)

if [ -z "$glmark2_score" ]; then
  glmark2_score=$(grep -E "Score:" glmark2.log | grep -oE '[0-9]+(\.[0-9]+)?' | tail -1)
fi

if [ -n "$glmark2_score" ]; then
  save_result "GPU_glmark2_score" "$glmark2_score" "pkt"
else
  echo "Nie udało się wyciągnąć wyniku GPU z glmark2, zapisuję N/A"
  save_result "GPU_glmark2_score" "N/A" "-"
fi

echo -e "\nUsuwam plik testowy..."
rm -f ./testfile

echo -e "\nBenchmark zakończony. Wyniki zapisano w pliku $RESULTS_FILE"
