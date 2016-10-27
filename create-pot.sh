#!/usr/bin/env bash
TARGET="lang"
LANG="wcmavatar"

echo "\n # Generating Translation #"
echo " # ---------------------- #"

echo " ==> Generating lang/ dir"
[[ -d "${TARGET}" ]] || mkdir "${TARGET}"

echo " ==> Generating l18n file"
xgettext Bootstrap.php src/**/*.php \
	--language=PHP \
	--indent \
	--from-code=UTF-8 \
	--package-name="${LANG}" \
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
	--default-domain="${LANG}.po" \
	--sort-by-file \
	--strict \
	--width=80 \
	--output-dir=lang \
	--msgid-bugs-address=wecodemore@gmail.com \
	--output=wcmavatar.pot

echo " ==> Testing if the file is valid\n"
msgfmt -cv "${TARGET}/${LANG}.pot"
echo "\n"