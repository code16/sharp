import Fuse from 'fuse.js';

export default class SearchStrategy {
    constructor({list, minQueryLength, searchKeys}) {
        this.options = {
            caseSensitive:false,
            include: [],
            minMatchCharLength: 1,
            shouldSort: true,
            tokenize: true,
            matchAllTokens: false,
            findAllMatches: false,
            id: null,
            keys: searchKeys || ['value'],
            location: 0,
            threshold: 0.0,
            distance: 0,
            maxPatternLength: 64,
        };

        this.fuse = new Fuse(list, this.options);

        this.minQueryLength = minQueryLength || 0;
    }

    search(querystring) {
        if(querystring.length < this.minQueryLength)
            return this.fuse.list;
        return this.fuse.search(querystring);
    }
}