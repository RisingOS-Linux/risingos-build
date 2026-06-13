PREFIX     ?= /usr
DESTDIR    ?=
BINDIR      = $(DESTDIR)$(PREFIX)/bin
LIBDIR      = $(DESTDIR)$(PREFIX)/lib/rising-ia
ASSETSDIR   = $(LIBDIR)/assets
APPSDIR     = $(DESTDIR)$(PREFIX)/share/applications

.PHONY: all install uninstall deb clean

all:
	@echo "rising-ia — nada que compilar (Python puro)"
	@echo "Usá: make install  o  make deb"

install:
	install -d $(BINDIR)
	install -d $(ASSETSDIR)/icons
	install -m 755 src/rising-ia          $(BINDIR)/rising-ia
	install -m 644 assets/style.css       $(ASSETSDIR)/style.css
	install -m 644 assets/icons/logo-256.png $(ASSETSDIR)/icons/logo-256.png
	install -d $(APPSDIR)
	install -m 644 rising-ia.desktop      $(APPSDIR)/rising-ia.desktop
	@echo "rising-ia instalado en $(PREFIX)"

uninstall:
	rm -f  $(BINDIR)/rising-ia
	rm -rf $(LIBDIR)
	rm -f  $(APPSDIR)/rising-ia.desktop
	@echo "rising-ia desinstalado"

deb:
	dpkg-buildpackage -us -uc -b
	@echo "Paquete .deb generado en el directorio padre"

clean:
	rm -rf debian/.debhelper debian/rising-ia debian/files debian/debhelper-build-stamp
	@echo "Limpieza completa"
