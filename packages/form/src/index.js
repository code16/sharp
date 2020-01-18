import routes, { formUrl } from './routes';


export default function (Vue, { router, store }) {
    router.addRoutes(routes);
}

export {
    formUrl,
}