import { NotificationType } from '../enums/notification-type.enum.js';
import { PaymentStatus } from '../enums/payment-status.enum.js';
import { MagicLinkData } from './magic-link.data.js';
import { PaymentResultData } from './payment-result.data.js';
import { TicketConfirmedData } from './ticket-confirmed.data.js';
import { TicketExpiredData } from './ticket-expired.data.js';
import { TicketOfferedData } from './ticket-offered.data.js';

const date = new Date('2026-12-01T18:30:00Z');

describe('NotificationData', () => {
  it('TicketOfferedData', () => {
    const data = new TicketOfferedData('Ada', 'T-1', 'Rock Fest', date);

    expect(data.type()).toBe(NotificationType.TICKET_OFFERED);
    expect(data.message()).toBe(
      'Hi Ada, ticket T-1 for Rock Fest is available for you. The offer expires on 1 Dec 2026, 18:30 UTC.',
    );
  });

  it('TicketConfirmedData', () => {
    const data = new TicketConfirmedData(
      'Ada',
      'T-1',
      'Rock Fest',
      'O2 Arena',
      date,
    );

    expect(data.type()).toBe(NotificationType.TICKET_CONFIRMED);
    expect(data.message()).toBe(
      'Hi Ada, your ticket T-1 for Rock Fest is confirmed. See you at O2 Arena on 1 Dec 2026, 18:30 UTC.',
    );
  });

  it('TicketExpiredData', () => {
    const data = new TicketExpiredData('Ada', 'T-1', 'Rock Fest');

    expect(data.type()).toBe(NotificationType.TICKET_EXPIRED);
    expect(data.message()).toBe(
      'Hi Ada, your ticket T-1 for Rock Fest has expired.',
    );
  });

  it('PaymentResultData reports success and failure', () => {
    const args = ['Ada', 'P-9', '£50.00', 'T-1', 'Rock Fest', date] as const;
    const ok = new PaymentResultData(
      args[0],
      PaymentStatus.SUCCESS,
      args[1],
      args[2],
      args[3],
      args[4],
      args[5],
    );
    const failed = new PaymentResultData(
      args[0],
      PaymentStatus.FAILED,
      args[1],
      args[2],
      args[3],
      args[4],
      args[5],
    );

    expect(ok.type()).toBe(NotificationType.PAYMENT_RESULT);
    expect(ok.message()).toContain('was successful');
    expect(failed.message()).toContain('failed');
    expect(ok.message()).toContain('P-9');
  });

  it('MagicLinkData', () => {
    const data = new MagicLinkData('Ada', 'https://x.test/l/abc', date);

    expect(data.type()).toBe(NotificationType.MAGIC_LINK);
    expect(data.message()).toBe(
      'Hi Ada, use this link to sign in: https://x.test/l/abc. It expires on 1 Dec 2026, 18:30 UTC.',
    );
  });
});
