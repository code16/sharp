import fr from 'element-ui/lib/locale/lang/fr';
import ru from 'element-ui/lib/locale/lang/ru-RU';
import es from 'element-ui/lib/locale/lang/es';
import en from 'element-ui/lib/locale/lang/en';

const languages = {
    fr, ru, es, en,
};

export function elLang() {
    const lang = document.documentElement.lang;
    return languages[lang] || languages.en;
}