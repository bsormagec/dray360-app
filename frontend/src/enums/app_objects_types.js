export const dictionaryItemsTypes = {
  template: 'template',
  itgContainer: 'itgcontainer'
}

export const statuses = {
  intakeAccepted: 'intake-accepted',
  intakeAcceptedDatafile: 'intake-accepted-datafile',
  intakeException: 'intake-exception',
  intakeRejected: 'intake-rejected',
  intakeStarted: 'intake-started',
  ocrCompleted: 'ocr-completed',
  ocrPostProcessingComplete: 'ocr-post-processing-complete',
  ocrPostProcessingError: 'ocr-post-processing-error',
  ocrWaiting: 'ocr-waiting',
  ocrTimedout: 'ocr-timedout',
  processOcrOutputFileComplete: 'process-ocr-output-file-complete',
  processOcrOutputFileError: 'process-ocr-output-file-error',
  uploadRequested: 'upload-requested',
  sendingToWint: 'sending-to-wint',
  failureSendingToWint: 'failure-sending-to-wint',
  successSendingToWint: 'success-sending-to-wint',
  shipmentCreatedByWint: 'shipment-created-by-wint',
  shipmentNotCreatedByWint: 'shipment-not-created-by-wint',
  updatingToWint: 'updating-to-wint',
  failureUpdatingToWint: 'failure-updating-to-wint',
  successUpdatingToWint: 'success-updating-to-wint',
  shipmentUpdatedByWint: 'shipment-updated-by-wint',
  shipmentNotUpdatedByWint: 'shipment-not-updated-by-wint',
  updatesPriorOrder: 'updates-prior-order',
  updatedBySubsequentOrder: 'updated-by-subsequent-order',
  successImageuplodingToBlackfl: 'success-imageuploding-to-blackfl',
  failureImageuplodingToBlackfl: 'failure-imageuploding-to-blackfl',
  untriedImageuplodingToBlackfl: 'untried-imageuploding-to-blackfl'
}

export const displayStatuses = {
  processing: 'Processing',
  exception: 'Exception',
  rejected: 'Rejected',
  intake: 'Intake',
  processed: 'Processed',
  sendingToTms: 'Sending to TMS',
  sentToTms: 'Sent to TMS',
  acceptedByTms: 'Accepted by TMS',
  tmsWarning: 'TMS Warning',
  tmsError: 'TMS Error'
}
