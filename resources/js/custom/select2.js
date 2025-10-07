export const loadSelect2 = function (selector, url) {
    $(selector).select2({
        ajax: {
            url: url,
            data: function (params) {
                let query = {
                    search: params.term,
                    page: params.page || 1
                };
                return query;
            },
            processResults: function (data) {
                return {
                    results: data
                }
            }
        }
    });
};
