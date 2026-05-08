#!/bin/bash
# Nuclei batch test script
# Usage: bash test-all.sh

NUCLEI="/opt/homebrew/bin/nuclei"
TEMPLATES_DIR="/Users/zer0p0int/Desktop/wordpress漏洞环境自动化生成/nuclei-templates"
RESULTS_FILE="/Users/zer0p0int/Desktop/wordpress漏洞环境自动化生成/test-results.txt"

# CVE -> port mapping
declare -A PORTS=(
  [CVE-2024-2876]=8088
  [CVE-2021-24931]=8089
  [CVE-2023-23488]=8090
  [CVE-2022-0201]=8091
  [CVE-2022-0271]=8092
  [CVE-2021-25032]=8093
  [CVE-2022-0952]=8094
  [CVE-2021-4436]=8095
  [CVE-2024-5084]=8096
  [CVE-2014-8799]=8097
  [CVE-2015-2755]=8098
  [CVE-2021-25052]=8099
  [CVE-2024-7313]=8100
  [CVE-2021-24340]=8101
  [CVE-2021-24731]=8102
  [CVE-2022-0169]=8103
  [CVE-2022-4059]=8104
  [CVE-2023-6360]=8105
  [CVE-2024-13496]=8106
  [CVE-2025-2011]=8107
  [CVE-2022-3982]=8108
  [CVE-2024-2667]=8109
  [CVE-2022-0148]=8110
  [CVE-2022-1724]=8111
  [CVE-2024-6517]=8113
)

# Auth required CVEs
AUTH_CVES="CVE-2015-2755 CVE-2021-25032 CVE-2021-25052 CVE-2022-0148 CVE-2024-7313"

echo "=== Nuclei Template Test Results ===" > "$RESULTS_FILE"
echo "Date: $(date)" >> "$RESULTS_FILE"
echo "" >> "$RESULTS_FILE"

pass=0
fail=0
skip=0

for cve in $(echo "${!PORTS[@]}" | tr ' ' '\n' | sort); do
  port=${PORTS[$cve]}
  template="${TEMPLATES_DIR}/${cve}.yaml"
  url="http://localhost:${port}"

  # Check if template exists
  if [ ! -f "$template" ]; then
    echo "SKIP $cve - template not found"
    echo "SKIP $cve - template not found" >> "$RESULTS_FILE"
    skip=$((skip + 1))
    continue
  fi

  # Check if port is up
  if ! curl -s -o /dev/null -w '' --max-time 3 "$url" 2>/dev/null; then
    echo "SKIP $cve - port $port not responding"
    echo "SKIP $cve - port $port not responding" >> "$RESULTS_FILE"
    skip=$((skip + 1))
    continue
  fi

  # Build nuclei command
  cmd="$NUCLEI -t $template -u $url -timeout 10 -rl 1 -silent"
  # Add auth if needed
  if echo "$AUTH_CVES" | grep -q "$cve"; then
    cmd="$cmd -V username=admin -V password=admin"
  fi

  echo -n "TEST $cve (port $port)... "
  output=$(eval "$cmd" 2>&1)
  exit_code=$?

  if [ $exit_code -eq 0 ] && [ -n "$output" ]; then
    echo "PASS"
    echo "PASS $cve (port $port)" >> "$RESULTS_FILE"
    echo "  Output: $output" >> "$RESULTS_FILE"
    pass=$((pass + 1))
  else
    echo "FAIL"
    echo "FAIL $cve (port $port)" >> "$RESULTS_FILE"
    echo "  Output: $output" >> "$RESULTS_FILE"
    fail=$((fail + 1))
  fi
done

echo ""
echo "=== Summary ==="
echo "PASS: $pass"
echo "FAIL: $fail"
echo "SKIP: $skip"
echo "TOTAL: $((pass + fail + skip))"

echo "" >> "$RESULTS_FILE"
echo "=== Summary ===" >> "$RESULTS_FILE"
echo "PASS: $pass" >> "$RESULTS_FILE"
echo "FAIL: $fail" >> "$RESULTS_FILE"
echo "SKIP: $skip" >> "$RESULTS_FILE"
