
## User-friendly targets.

all: inline preload

preload: watcher.so
	php -d opcache.preload="preloader.php" -d opcache.enable_cli=true preload.php

inline: watcher.so
	php -d opcache.enable_cli=true inline.php

clean:
	rm -f *.o *.so main


## Building dependencies

watcher.o: watcher.h watcher.c
	# -c means "make an object file"
	gcc -c watcher.c

watcher.so: watcher.o
	# Wrap the object file into a shared object.
	gcc -shared -o watcher.so watcher.o -lm

main.o: main.c
	# Compile the main file into object code.
	gcc -c main.c

main: main.o watcher.o
	# Squish the main object code and watcher object code into one executable file.
	# The -lm tells the linker to also include the math library, which is named "m".  Or rather, libm.
	gcc -o main main.o watcher.o -lm
	chmod +x main
