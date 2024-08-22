#!/bin/bash
export CUDA_VISIBLE_DEVICES=-1
source ./venv/bin/activate

while true; do
  python3 $PWD/main.py ./MLPTTS
done
