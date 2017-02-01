EXPORTS	:= $(wildcard exports/*/)
RULE	?= all

.PHONY: all clean distclean install gen_exports link_exports $(EXPORTS)

all: gen_exports
	$(MAKE) link_exports

clean:
	$(MAKE) RULE=clean

distclean:
	rm -f exports.xml
	rm -rf ./exports

install:
	$(MAKE) RULE=install

gen_exports: genexports.php exports.xml
	php genexports.php

link_exports: $(EXPORTS)

exports.xml:
	cp ./ps3ida/ps3.xml ./$@

$(EXPORTS):
	$(MAKE) -C $@ $(RULE)
