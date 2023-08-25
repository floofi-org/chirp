#!/bin/bash
python -m venv ./venv
source ./venv/bin/activate
pip install numpy matplotlib
pip install tqdm
pip install resampy
pip install git+https://github.com/wkentaro/gdown.git
pip install unidecode
pip install tensorflow
pip install inflect
pip install torch
pip install librosa
pip install flask