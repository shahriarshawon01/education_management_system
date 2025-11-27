export default {
    componentUpdated: function (el, binding) {
        $(el).off().select2('destroy');
        let allowClear = $(el).data("allowclear") == false ? false : true;
        let options = binding.value || {};
        let configurationObject = allowClear ? {
            data: options,
            placeholder: 'Select',
            allowClear: allowClear
        } : {
            data: options,

        }

        $(el).select2(configurationObject).on("select2:select", (e) => {
            el.dispatchEvent(new Event('change', { target: e.target }));
        })
        if ($(el).closest('.modal').length) {
            $(el).select2({
                dropdownParent: $(el).closest('.modal')
            });
        }
    },
    inserted: function (el, binding) {
        let allowClear = $(el).data("allowclear") == false ? false : true;
        let options = binding.value || {};
        let configurationObject = allowClear ? {
            data: options,
            placeholder: 'Select',
            allowClear: allowClear
        } : {
            data: options,

        }

        $(el).select2(configurationObject).on("select2:select", (e) => {
            el.dispatchEvent(new Event('change', { target: e.target }));
        })

    },
    unbind: function (el, binding) {
        $(el).off().select2('destroy');
    }
}
