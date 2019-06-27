import {
    parseRange,
    serializeRange,
} from "../../util/querystring";

describe('querystring utils', () => {
    test('parseRange', () => {
        expect(parseRange(null)).toEqual({
            start: null,
            end: null,
        });
        expect(parseRange('..20190605')).toEqual({
            start: null,
            end:  new Date(2019, 5, 5),
        });
        expect(parseRange('20190601..')).toEqual({
            start: new Date(2019, 5, 1),
            end: null,
        });
        expect(parseRange('20190601..20190605')).toEqual({
            start: new Date(2019, 5, 1),
            end: new Date(2019, 5, 5),
        });
    });

    test('serializeRange', () => {
        expect(serializeRange(null)).toEqual(null);

        expect(serializeRange({
            start: null,
            end: null,
        })).toEqual(null);

        expect(serializeRange({
            start: new Date(Date.UTC(2019, 5, 1)),
            end: null,
        })).toEqual('20190601..');

        expect(serializeRange({
            start: null,
            end: new Date(Date.UTC(2019, 5, 5)),
        })).toEqual('..20190605');

        expect(serializeRange({
            start: new Date(Date.UTC(2019, 5, 1)),
            end: new Date(Date.UTC(2019, 5, 5)),
        })).toEqual('20190601..20190605');
    });
});