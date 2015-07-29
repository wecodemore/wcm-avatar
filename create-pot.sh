#!/bin/sh
mkdir lang
xgettext Bootstrap.php src/**/*.php \
	--language=PHP \
	--indent \
	--omit-header \
	--package-name=wcmavatar \
	--keyword=__:1 \
	--keyword=_e:1 \
	--keyword=_x:1,2c \
	--keyword=esc_html__:1 \
	--keyword=esc_html_e:1 \
	--keyword=esc_html_x:1,2c \
	--keyword=esc_attr__:1 \
	--keyword=esc_attr_e:1 \
	--keyword=esc_attr_x:1,2c \
	--keyword=_ex:1,2c \
	--keyword=_n:1,2,4d \
	--keyword=_nx:1,2,4c \
	--keyword=_n_noop:1,2 \
	--keyword=_nx_noop:1,2,3c \
	--keyword=ngettext:1,2 \
	--default-domain=wcmavatar.po \
	--sort-by-file \
	--width=80 \
	--output-dir=lang \
	--output=wcmavatar.pot
