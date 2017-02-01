SYSMODULE_EXPORTS	:= $(wildcard sysmodule_exports/*/)
RULE				?= all

.PHONY: all clean distclean install gen_exports link_exports $(SYSMODULE_EXPORTS)

all: gen_exports
	$(MAKE) link_exports

clean:
	$(MAKE) RULE=clean

distclean:
	rm -f exports.xml
	rm -rf ./sysmodule_exports

install:
	$(MAKE) RULE=install

gen_exports: genexports.php exports.xml
	php genexports.php

link_exports: $(SYSMODULE_EXPORTS)

exports.xml:
	cp ./ps3ida/ps3.xml ./$@

$(SYSMODULE_EXPORTS):
	$(MAKE) -C $@ $(RULE)
