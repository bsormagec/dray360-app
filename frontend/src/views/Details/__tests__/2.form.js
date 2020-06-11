import formFetching from './3.formFetching'
import formFieldHighlighting from './4.formFieldHighlighting'
import formFieldEditingViewMode from './5.formFieldEditingViewMode'
// import formFieldEditingEditMode from './6.formFieldEditingEditMode'

export default () =>
  describe('form', () => {
    formFetching()
    formFieldHighlighting()
    formFieldEditingViewMode()
    // formFieldEditingEditMode()
  })
