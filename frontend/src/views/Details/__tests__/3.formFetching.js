import Details from '@/views/Details/Details'
import store from '@/store/modules/orders'
import mount from '@/utils/for_tests/mount_authenticated'
import waitForResponse from '@/utils/for_tests/wait_for_response'

export default () =>
  describe('formFetching', () => {
    it('populates form with order 119 data', async () => {
      const wrapper = await mount(Details, { store })
      await waitForResponse.wait()

      for (const [key, value] of Object.entries(fieldsAndValues())) {
        const elValue = wrapper.find(`div[test-id="${key}"] .field__value`).text()
        expect(elValue).toMatch(value)
      }
    })
  })

function fieldsAndValues () {
  // Keys based on test-id
  return {
    'shipment designation': 'International',
    'shipment direction': 'Import',
    'unit number': 'OOCU7217616',
    size: '40HC',
    'reference number': 'SSI100072144',
    'house BOL MAWB': 'SZSE2000255',
    'port ramp of destination': `BNSF LOGISTICS PARK H572
26353 ELWOOD INTERNATIONAL PORT RD
ELWOOD IL 60421
UNITED STATES
Contact:`,
    'Port Ramp of Destination matched': 'BNSF - Chicago (Logistics Park) 26664 Elwood International Port Road Elwood, IL 60421',
    'bill to': `Jet-Speed Logistics (USA) LLC
900 N. Arlington Heights Rd., Suite 150
Itasca. Illinois 60143. U.S.A.
Tel: (630) 595-5601
Fax: (630) 595-6361`,
    'pickup full': `BNSF LOGISTICS PARK H572
26353 ELWOOD INTERNATIONAL PORT RD
ELWOOD IL 60421
UNITED STATES
Contact:`,
    'deliver to full': `3030 S SYLVANIA AVE STE L
STURTEVANT WI 53177
UNITED STATES
Contact:`,
    'pickup empty': `3030 S SYLVANIA AVE STE L
STURTEVANT WI 53177
UNITED STATES
Contact:`,
    'deliver to empty': `BNSF LOGISTICS PARK H572
26353 ELWOOD INTERNATIONAL PORT RD
ELWOOD IL 60421
UNITED STATES
Contact:`,
    'ìtem 1': 'DEEP EDDY NESTING CUBE, FARM RACK'
  }
}
