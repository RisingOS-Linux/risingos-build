PREFIX ?= /usr

.PHONY: install uninstall deb clean

install:
	install -D -m 0755 src/rising-update $(DESTDIR)$(PREFIX)/bin/rising-update
	install -D -m 0644 assets/rising-update.desktop $(DESTDIR)/etc/xdg/autostart/rising-update.desktop
	install -D -m 0440 assets/rising-update.sudoers $(DESTDIR)/etc/sudoers.d/rising-update
	install -D -m 0644 assets/icons/rising-update.svg \
		$(DESTDIR)$(PREFIX)/share/icons/hicolor/scalable/status/rising-update.svg
	install -D -m 0644 assets/icons/rising-update-attention.svg \
		$(DESTDIR)$(PREFIX)/share/icons/hicolor/scalable/status/rising-update-attention.svg
	@echo "Instalado. Si el sudoers es nuevo, correr: sudo visudo -cf /etc/sudoers.d/rising-update"

uninstall:
	rm -f $(DESTDIR)$(PREFIX)/bin/rising-update
	rm -f $(DESTDIR)/etc/xdg/autostart/rising-update.desktop
	rm -f $(DESTDIR)/etc/sudoers.d/rising-update
	rm -f $(DESTDIR)$(PREFIX)/share/icons/hicolor/scalable/status/rising-update.svg
	rm -f $(DESTDIR)$(PREFIX)/share/icons/hicolor/scalable/status/rising-update-attention.svg

deb:
	dpkg-buildpackage -us -uc -b

clean:
	rm -f ../rising-update_*.deb ../rising-update_*.buildinfo ../rising-update_*.changes
