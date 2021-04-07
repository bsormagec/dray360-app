export const dictionaryItemsTypes = {
  template: 'template',
  itgContainer: 'itgcontainer',
  carrier: 'carrier',
  vessel: 'vessel'
}

export const objectLocks = {
  refreshIntervalTime: 10000,
  objectTypes: {
    request: 'request',
    order: 'order'
  },
  lockTypes: {
    claimLock: 'claim-lock',
    selectRequest: 'select-request',
    openOrder: 'open-order'
  }
}

export const statuses = {
  intakeAccepted: 'intake-accepted',
  intakeAcceptedDatafile: 'intake-accepted-datafile',
  intakeException: 'intake-exception',
  intakeRejected: 'intake-rejected',
  intakeStarted: 'intake-started',
  ocrCompleted: 'ocr-completed',
  ocrPostProcessingReview: 'ocr-post-processing-review',
  processOcrOutputFileReview: 'process-ocr-output-file-review',
  ocrPostProcessingComplete: 'ocr-post-processing-complete',
  ocrPostProcessingError: 'ocr-post-processing-error',
  ocrPostProcessingAutosubmitted: 'ocr-post-processing-autosubmited',
  replicatedFromExistingOrder: 'replicated-from-existing-order',
  ocrWaiting: 'ocr-waiting',
  ocrTimedout: 'ocr-timedout',
  processOcrOutputFileComplete: 'process-ocr-output-file-complete',
  processOcrOutputFileError: 'process-ocr-output-file-error',
  uploadRequested: 'upload-requested',

  sendingToWint: 'sending-to-wint',
  autoSendingToWint: 'auto-sending-to-wint',
  failureSendingToWint: 'failure-sending-to-wint',
  successSendingToWint: 'success-sending-to-wint',
  shipmentCreatedByWint: 'shipment-created-by-wint',
  shipmentNotCreatedByWint: 'shipment-not-created-by-wint',
  updatingToWint: 'updating-to-wint',
  failureUpdatingToWint: 'failure-updating-to-wint',
  successUpdatingToWint: 'success-updating-to-wint',
  shipmentUpdatedByWint: 'shipment-updated-by-wint',
  shipmentNotUpdatedByWint: 'shipment-not-updated-by-wint',

  sendingToChainio: 'sending-to-chainio',
  autoSendingToChainio: 'auto-sending-to-chainio',
  successSendingToChainio: 'success-sending-to-chainio',
  failureSendingToChainio: 'failure-sending-to-chainio',
  shipmentCreatedByChainio: 'shipment-created-by-chainio',
  shipmentNotCreatedByChainio: 'shipment-not-created-by-chainio',

  updatesPriorOrder: 'updates-prior-order',
  requestMarkedDone: 'request-marked-done',
  requestMarkedUndone: 'request-marked-undone',
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
  autoSubmitted: 'Auto Submitted',
  sendingToTms: 'Sending to TMS',
  sentToTms: 'Sent to TMS',
  acceptedByTms: 'Accepted by TMS',
  tmsWarning: 'TMS Warning',
  tmsError: 'TMS Error',
  needsReview: 'Needs Review',
  replication: 'Replication',
  markDone: 'Marked Complete',
  markUndone: 'Marked not Complete'
}
