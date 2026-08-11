const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('particular_payments', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    particular: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    consignee: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    po_no: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    invoice: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    due_period: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    datefrom: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    bill_dis_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    amount: {
      type: DataTypes.DECIMAL(15,2),
      allowNull: true,
      defaultValue: 0.00
    },
    created: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    modified: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    status: {
      type: DataTypes.ENUM('P','C'),
      allowNull: true,
      defaultValue: "P"
    }
  }, {
    sequelize,
    tableName: 'particular_payments',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
