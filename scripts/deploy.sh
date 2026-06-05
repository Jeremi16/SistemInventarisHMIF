#!/usr/bin/env bash
set -euo pipefail

bash scripts/deploy-build.sh
bash scripts/deploy-release.sh
