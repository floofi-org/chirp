from time import sleep
from thread import *

if not os.path.exists("./outputs"):
    os.makedirs("./outputs")

while True:
    try:
        for d in os.listdir("./outputs"):
            if d.startswith("."):
                continue
            if not os.path.exists("./outputs/" + d + "/input.txt"):
                continue
            if os.path.exists("./outputs/" + d + "/complete.txt"):
                continue
            if os.path.exists("./outputs/" + d + "/process.txt"):
                continue

            if os.path.exists("./outputs/" + d + "/process.txt"):
                continue

            with open("./outputs/" + d + "/process.txt", "w") as f:
                f.write(str(os.getpid()))

            with open("./outputs/" + d + "/input.txt", "r") as f:
                input = f.read()

                end_to_end_infer(input, not pronounciation_dictionary, show_graphs, d)

            with open("./outputs/" + d + "/complete.txt", "a") as f:
                f.write("")
    except KeyboardInterrupt:
        break