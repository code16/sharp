import fr from 'element-ui/lib/locale/lang/fr';
import ru from 'element-ui/lib/locale/lang/ru-RU';
import es from 'element-ui/lib/locale/lang/es';
import en from 'element-ui/lib/locale/lang/en';
import de from 'element-ui/lib/locale/lang/de';

const languages = {
    fr, ru, es, en, de
};

export function elLang() {
    const lang = document.documentElement.lang;
    return languages[lang] || languages.en;
}